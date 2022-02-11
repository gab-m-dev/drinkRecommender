<?php

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
$sql = "SELECT * FROM Drinks ORDER BY RAND() Limit 10";

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

$app->get('/api-drinks/{drink}', function (Request $request, Response $response) {

    $drink = $request->getAttribute('drink');

    //if only on character provided fail -> not allowd
    if(strlen($drink) > 1)
    {
        $sql_similarity = "SELECT D2.Name, D_S.Value_Ingrediants, D2.Category, D2.Ingrediants, D2.Alcohol, D2.Glass, D2.Instructions FROM Drinks AS D,Drinks_Similarity AS D_S, Drinks AS D2 WHERE D.Name = :drink AND D.ID = D_S.fk_Drink1 AND D2.ID = D_S.fk_Drink2 ORDER BY D_S.Value_Ingrediants DESC limit 1,10";

        try {
            $db = new Db();
            $conn = $db->connect();
            $stmt = $conn->prepare($sql_similarity);
            $stmt->bindParam('drink', $drink);
            $stmt->execute();

            $drinks = $stmt->fetchAll(PDO::FETCH_OBJ);

            //if empty check if name is like
            if (empty($drinks)){
                $sql_like = "SELECT Name, Category, Ingrediants, Alcohol, Glass, Instructions FROM Drinks WHERE Name LIKE CONCAT('%',:drink,'%') ORDER BY RAND() Limit 10";
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