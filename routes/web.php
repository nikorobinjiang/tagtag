<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', 'HomeController@index');
Auth::routes();

//今日头条回调地址
Route::get('oauth2s/toutiao/callback', 'Oauth2s\ToutiaoController@callback')->name('oauth2_toutiao_callback');
Route::get('oauth2s/result', 'Oauth2s\ResultController@index')->name('oauth2_result_index');

Route::group(['middleware' => 'auth'], function () {
    if (env('APP_ENV') == 'local') {
        Route::get('logs', function () {
            // 过滤危险请求
            $inputBans = collect(\Request::keys())->filter(function ($key) {
                return in_array($key, ['del', 'clean']);
            })->values();
            if ($inputBans->count() <= 0) {
                $logger = new \Rap2hpoutre\LaravelLogViewer\LogViewerController();
                return $logger->index();
            } else {
                return '该类型请求已经被禁止';
            }
        });
    }
    Route::get('/home/getData', 'HomeController@getData')->name('home_data');
    // Route::get('/countTo','HomeController@countTo');
    // Route::get('/trendCount','HomeController@trendCount');

    Route::get('logout', 'Auth\LoginController@logout')->name('logout');
    // 上传辅助
    Route::post('upload/chunk', 'UploadController@chunk')->name('upload_chunk');
    Route::post('upload', 'UploadController@store')->name('upload_store');
    Route::any('upload/download', 'UploadController@download')->name('upload_download');

    Route::resources([
        // 广告位
        'advertising/ad_space' => 'Advertising\AdSpaceController',
        
    ]);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('introduce', 'Api\IntroduceController');
