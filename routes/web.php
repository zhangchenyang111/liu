<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('dec','Index@dec');
$router->post('dec_no','Index@dec_no');
$router->post('sign','Index@sign');


//$router->post('res','Index@res');
//$router->post('login','Index@login');
//$router->post('loginToken','Index@loginToken');

$router->post('res','LoginController@res');
$router->post('login','LoginController@login');
$router->post('loginToken','LoginController@loginToken');
$router->get('urlget','LoginController@urlget');
//$router->get('center','LoginController@center');

$router->group(['middleware' => 'token'], function () use($router) {
    $router->get('center','LoginController@center');
});

