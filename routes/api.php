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



// nv.permission:allow,root,admin
// 权限
Route::group(['middleware' => ['auth.api:api', 'nv.permission']], function () {
});

// Route::middleware('auth.api:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// 今日头条
Route::group(
    [
        'prefix' => 'dist',
        'namespace' => 'Dist',
        'as' => 'dist.',
    ],
    function () {
        Route::get('/toutiao/account', 'ToutiaoAccountController@index')->name('dist.toutiao.account');;
        Route::get('/toutiao/token_refresh', 'ToutiaoTokenRefreshController@index')->name('dist.toutiao.token_refresh');;
    }
);

Route::apiResource('introduce', 'IntroduceController');
Route::get('introduce/index', 'IntroduceController@index');

