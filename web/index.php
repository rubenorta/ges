<?php
require_once __DIR__.'/../vendor/autoload.php'; 

$app = new Silex\Application();

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
        'db.options' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'ges',
            'user'      => 'root',
            'password'  => 'pass',
            'charset'   => 'utf8',
        ),
    ));

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Just for checking that the server is working
 */
$app->get('/check', function () use ($app){
    $checkSql = "SELECT count(id) as amount FROM data";
    $amount = $app['db']->fetchAssoc($checkSql)['amount'];
	return 'Server is online and data has '.$amount.' records.';
});

$app->get('/data', function () use ($app) {
    $getSql = "SELECT * FROM data";
    $data = $app['db']->fetchAll($getSql);
    return $app->json($data);
});

$app->post('/data', function (Request $request) use ($app) {
    $concept = $request->get('concept');
    $amount = $request->get('amount');
    $saveSql = "INSERT INTO data (id, concept, amount) VALUES (null, ?, ?)";
    $result = $app['db']->executeUpdate($saveSql, array($concept, $amount));
    return new Response('Record saved: '.$result, 200);
});

$app->after(function (Request $request, Response $response) {
        // we could be more restrictive here
        $response->headers->set('Access-Control-Allow-Origin', '*');
    });

$app->run(); 
