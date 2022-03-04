<?php

header("Access-Control-Allow-Origin: https://drinks.gabormuff.info");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-type: text/plain; charset=utf-8');

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\DB;

require_once __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

//Login und Erstellung JWT
$app->post('/login', function (Request $request, Response $response, $arg){
    
    $body = $request->getParsedBody();
    $config = include(__DIR__ . '/src/local.php');

    if ($config['user']['username'] == $body['username'] && password_verify($body['password'], $config['user']['password'])){
        $token = [
            "iss" => "drinks.gabormuff.info",
            "iat" => time(),
            "exp" => time() + 60*60,
            "data" => [
            "username" => $config['user']['username']
            ]
            ];
            $jwt = JWT::encode($token, $config['secret'], 'HS256');
            return $response->withJson([
            'success' => true,
            'message' => "Login Successfull",
            'jwt' => $jwt
            ]);
    } else {
        return $response->withJson([
            'success' => false,
            'message' => "Username or Password false"
            ]);
        }
});

// get usage data if you are loged in
$app->get('/usagedata', function (Request $request, Response $response, $arg){

    $config = include(__DIR__ . '/src/local.php');
	$jwt = $request->getHeaders();
    $jwt = str_replace('Bearer ', '', $jwt['Authorization'][0]);

    try {
        $decoded = JWT::decode($jwt, new key ($config['secret'],'HS256'));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode('No access, please log in!'));
                return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
    }

    if (isset($decoded)){
        
        $sql = "SELECT similarDrinks, searchedDrinks FROM UsageData";
    
        try {
            $db = new Db();
            $conn = $db->connect();
            $stmt = $conn->query($sql);
            $usage = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            
            $response->getBody()->write(json_encode($usage));
            return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
        } catch (PDOException $e) {
            $error = array("message" => $e->getMessage());
            $response->getBody()->write(json_encode($error));
            return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
        }
    }
 
});

//get 10 random Drinks
$app->get('/api-drinks', function (Request $request, Response $response) {
    $sql = "SELECT Name, Category, Ingrediants, Alcohol, Glass, Instructions FROM Drinks ORDER BY RAND() Limit 10";
    $inc = "UPDATE UsageData SET searchedDrinks = searchedDrinks + 1";

    try {
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->query($sql);
        $drinks = $stmt->fetchAll(PDO::FETCH_OBJ);

        //increment number of searched drinks 
        $conn->query($inc);

        $db = null;
        
        $response->getBody()->write(json_encode($drinks));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
        "message" => $e->getMessage()
        );
        $response->getBody()->write(json_encode($error));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

//get drinks according to ingrediants
$app->get('/api-drinks/ing', function (Request $request, Response $response) {

    //ing input is an array -> ing[]=bla&ing[]=bla...
    $ing = $_GET['ing'];

    //check for limit argument
    if(isset($_GET['limit']) AND $_GET['limit'] != 10 AND $_GET['limit'] > 0 AND $_GET['limit'] < 20){
        $limit = $_GET['limit'];
    }else{
        $limit = 10;
    }

    //if only on character provided then fail -> not allowd
    if(strlen($ing[0]) > 1)
    {
        //create sql statement to check for ingradiants ingrediants
        $sql = "SELECT Name, Category, Ingrediants, Alcohol, Glass, Instructions FROM Drinks WHERE ";
        $inc = "UPDATE UsageData SET searchedDrinks = searchedDrinks + 1";

        for($i = 0; $i < count($ing); $i++){
            if(($i + 1) == count($ing)){
                $sql = $sql . "LOCATE(:ing{$i},LOWER(Ingrediants)) ORDER BY RAND() Limit $limit";
            } else{
                $sql = $sql . "LOCATE(:ing{$i},LOWER(Ingrediants)) AND ";
            }
        }
        
        try {
            $db = new Db();
            $conn = $db->connect();
            $stmt = $conn->prepare($sql);
            for($i = 0; $i < count($ing); $i++){
                $stmt->bindParam("ing{$i}", $ing[$i]);
            }
            
            $stmt->execute();
            $drinks = $stmt->fetchAll(PDO::FETCH_OBJ);

            //increment number of searched drinks 
            $conn->query($inc);

            $db = null;
            
            $response->getBody()->write(json_encode($drinks));
            return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);

            } catch (PDOException $e) {
                $error = array(
                "message" => $e->getMessage()
                );
                $response->getBody()->write(json_encode($error));
                return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
            }
    } else{
        $response->getBody()->write(json_encode('No such drink!'));
                return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
    } 
});

//get drinks recepies
$app->get('/api-drinks/rec/{drink}', function (Request $request, Response $response) {

    $drink = $request->getAttribute('drink');

    //check for limit argument
    if(isset($_GET['limit']) AND $_GET['limit'] != 10 AND $_GET['limit'] > 0 AND $_GET['limit'] < 20){
        $limit = $_GET['limit'];
    }else{
        $limit = 10;
    }

    //if only on character provided then fail -> not allowd
    if(strlen($drink) > 1)
    {
        $sql_like = "SELECT Name, Category, Ingrediants, Alcohol, Glass, Instructions FROM Drinks WHERE Name LIKE CONCAT('%',:drink,'%') Limit $limit";
        $inc = "UPDATE UsageData SET searchedDrinks = searchedDrinks + 1";
        
        try {
            $db = new Db();
            $conn = $db->connect();
            $stmt = $conn->prepare($sql_like);
            $stmt->bindParam('drink', $drink);
            $stmt->execute();
            $drinks = $stmt->fetchAll(PDO::FETCH_OBJ);

            //increment number of searched drinks 
            $conn->query($inc);

            $db = null;
            
            $response->getBody()->write(json_encode($drinks));
            return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
            } catch (PDOException $e) {
                $error = array(
                "message" => $e->getMessage()
                );
                $response->getBody()->write(json_encode($error));
                return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
            }
    } else{
        $response->getBody()->write(json_encode('No such drink!'));
                return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
    } 
});

//get similar drinks
$app->get('/api-drinks/{drink}', function (Request $request, Response $response) {

    $drink = $request->getAttribute('drink');
    
    // check weather to search for similarity by ingrediants or instructions
    if(isset($_GET['sim']) AND $_GET['sim'] == 'inst'){
        $similarity = 'D_S.Value_Instructions';
    }
    else{
        $similarity = 'D_S.Value_Ingrediants';
    }

    //check for limit argument
    if(isset($_GET['limit']) AND $_GET['limit'] != 10 AND $_GET['limit'] > 0 AND $_GET['limit'] < 20){
        $limit = $_GET['limit'];
    }else{
        $limit = 10;
    }

    //if only on character provided then fail -> not allowd
    if(strlen($drink) > 1)
    {
        $sql_similarity = "SELECT D2.Name, $similarity, D2.Category, D2.Ingrediants, D2.Alcohol, D2.Glass, D2.Instructions FROM Drinks AS D,Drinks_Similarity AS D_S, Drinks AS D2 WHERE D.Name = :drink AND D.ID = D_S.fk_Drink1 AND D2.ID = D_S.fk_Drink2 ORDER BY $similarity DESC limit 1,$limit";
        $inc = "UPDATE UsageData SET similarDrinks = similarDrinks + 1";

        try {
            $db = new Db();
            $conn = $db->connect();
            $stmt = $conn->prepare($sql_similarity);
            $stmt->bindParam('drink', $drink);
            $stmt->execute();
            $drinks = $stmt->fetchAll(PDO::FETCH_OBJ);
			
			if (!empty($drinks)){
				var_dump($drinks);
				
				
			}
			

            //if empty check if name is like
            if (empty($drinks)){
                $sql_like = "SELECT Name, Category, Ingrediants, Alcohol, Glass, Instructions FROM Drinks WHERE Name LIKE CONCAT('%',:drink,'%') ORDER BY RAND() Limit $limit";
                $inc = "UPDATE UsageData SET searchedDrinks = searchedDrinks + 1";

                $stmt = $conn->prepare($sql_like);
                $stmt->bindParam('drink', $drink);
                $stmt->execute();
                $drinks = $stmt->fetchAll(PDO::FETCH_OBJ);
            }
			
            //increment number of searched drinks 
            $conn->query($inc);
            $db = null;
            
            $response->getBody()->write(json_encode($drinks));
            return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
            } catch (PDOException $e) {
                $error = array(
                "message" => $e->getMessage()
                );
                $response->getBody()->write(json_encode("hier passiert der Fehler!"));
                return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
            }
    } else{
        $response->getBody()->write(json_encode('No such drink!'));
                return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
    } 
});

$app->run();