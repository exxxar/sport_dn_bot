<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/range","HomeController@getRange");

Route::prefix('users')->group(function (){
    Route::post("/phone","WelcomeController@phone");

    Route::prefix('promo')->group(function (){
        Route::get("/list","WelcomeController@getList"); //step 0

        Route::post("/validate","WelcomeController@promoValidate"); //step 1

        Route::post("/check","WelcomeController@check"); //step 2
    });

});

Route::post('/send-request', 'WelcomeController@sendRequest')->name("callback.request");
Route::get('/ingredients/{type}', 'WelcomeController@getIngredients')->where(["type"=>"[0-9]{1}"]);
Route::get('/products/get/{id}', 'WelcomeController@getProduct')->where(["type"=>"[0-9]+"]);


