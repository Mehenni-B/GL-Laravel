<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/v1')->group(function () {

    // ---------------- AUTH ROUTES ---------------------------------------

    Route::prefix('/auth')->group(function () {
        Route::post('login', 'Api\V1\Auth\AuthController@login');
        Route::post('register', 'Api\V1\Auth\AuthController@register');
    });

    // ---------------- END AUTH ROUTES ---------------------------------------


    // ---------------- USER ROUTES ---------------------------------------

    Route::group(['middleware' => ['auth:api']], function () {
        Route::prefix('/user')->group(function () {
            Route::get('', 'Api\V1\User\UserController@index');
            Route::post('/update', 'Api\V1\User\UserController@update');

            // ---------------- PROJECT ROUTES ---------------------------------------

            Route::prefix('/project')->group(function () {
                Route::get('', 'Api\V1\User\ProjectController@index');
                Route::get('/{project}', 'Api\V1\User\ProjectController@project');
                Route::post('create', 'Api\V1\User\ProjectController@create');
                Route::post('update/{project}', 'Api\V1\User\ProjectController@update');
                Route::post('delete/{project}', 'Api\V1\User\ProjectController@delete');
            });

            // ---------------- END PROJECT ROUTES ---------------------------------------

        });
    });
    // ---------------- END USER ROUTES ---------------------------------------

});
