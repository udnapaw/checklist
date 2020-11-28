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

$router->group(['middleware' => 'auth', 'prefix' => 'api'], function ($router) {
    $router->get('profile', 'AuthController@profile');
    $router->post('checklists', 'ChecklistController@store');
    $router->get('checklists/items', [
        'as'    => 'get.allItems',
        'uses'  => 'ItemController@getAllItems'
    ]);
    $router->get('checklists/items/summaries', 'ItemController@summaryItem');
    $router->get('checklists/{checklistId}', [
        'as'    => 'get.checklist',
        'uses'  => 'ChecklistController@getByChecklistId'
    ]);
    $router->patch('checklists/{checklistId}', 'ChecklistController@update');
    $router->delete('checklists/{checklistId}', 'ChecklistController@delete');
    $router->get('checklists', 'ChecklistController@getList');

    $router->post('checklists/{checklistId}/items', [
        'as'    => 'post.checklistItems',
        'uses'  => 'ItemController@store'
    ]);
    $router->get('checklists/{checklistId}/items/{itemId}', [
        'as'    => 'get.checklistItem',
        'uses'  => 'ItemController@getChecklistItem'
    ]);
    $router->patch('checklists/{checklistId}/items/{itemId}', 'ItemController@update');
    $router->delete('checklists/{checklistId}/items/{itemId}', 'ItemController@delete');
    $router->post('checklists/{checklistId}/items/_bulk', 'ItemController@updateBulk');
    $router->post('checklists/complete', 'ItemController@complete');
    $router->post('checklists/incomplete', 'ItemController@incomplete');
    $router->get('checklists/{checklistId}/items', 'ItemController@itemsInGivenChecklists');
    $router->post('checklists/templates', 'ChecklistTemplateController@store');
    $router->get('checklists/templates', 'ChecklistTemplateController@listAll');    
    $router->get('checklists/templates/{templateId}', [
        'as'        => 'get.templates',
        'uses'      => 'ChecklistTemplateController@get'
    ]);
    $router->patch('checklists/templates/{templateId}', 'ChecklistTemplateController@update');
    $router->delete('checklists/templates/{templateId}', 'ChecklistTemplateController@delete');
    $router->post('checklists/templates/{templateId}/assigns', 'ChecklistTemplateController@assign');    

});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
});
