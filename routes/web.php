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

$router->group(['middleware' => 'auth','prefix' => 'api'], function ($router) 
{
    $router->get('profile', 'AuthController@profile');
    $router->post('checklists', 'ChecklistController@store');
    $router->get('checklists/{checklistId}', 'ChecklistController@getByChecklistId');
    $router->patch('checklists/{checklistId}', 'ChecklistController@update');
    $router->delete('checklists/{checklistId}', 'ChecklistController@delete');
});

$router->group(['prefix' => 'api'], function () use ($router) 
{
   $router->post('register', 'AuthController@register');
   $router->post('login', 'AuthController@login');
});