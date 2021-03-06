<?php
use \Illuminate\Http\Request;
/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('users/login', 'AuthController@login');
    $router->get('master/agama', 'AgamaController@getAgama');
    $router->post('master/createpasien', 'PasienController@createPasien');
    $router->post('master/dokter', 'PegawaiController@getPegawaiAdmisi');
    $router->post('master/pasien', 'PasienController@getPasien');
});