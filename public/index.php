<?php

// nur Temp!
header("Access-Control-Allow-Origin: *");

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use App\Models\DB;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
   $response->getBody()->write('Hello World!');
   return $response;
});

$app->get('/api-drinks', function (Request $request, Response $response) {
$sql = "SELECT Name, Category, Ingrediants, Alcohol, Glass, Instructions FROM Drinks ORDER BY RAND() Limit 10";

    try {
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->query($sql);
        $drinks = $stmt->fetchAll(PDO::FETCH_OBJ);
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

        try {
            $db = new Db();
            $conn = $db->connect();
            $stmt = $conn->prepare($sql_similarity);
            $stmt->bindParam('drink', $drink);
            $stmt->execute();

            $drinks = $stmt->fetchAll(PDO::FETCH_OBJ);

            //if empty check if name is like
            if (empty($drinks)){
                $sql_like = "SELECT Name, Category, Ingrediants, Alcohol, Glass, Instructions FROM Drinks WHERE Name LIKE CONCAT('%',:drink,'%') ORDER BY RAND() Limit $limit";
                $stmt = $conn->prepare($sql_like);
                $stmt->bindParam('drink', $drink);
                $stmt->execute();
                $drinks = $stmt->fetchAll(PDO::FETCH_OBJ);
            }
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


$app->run();