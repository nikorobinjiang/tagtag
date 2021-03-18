<?php

use Illuminate\Http\Request;

use App\Models\Advertisement;
use App\Models\AdMaterial;
use App\Http\Resources\AdvertisementCollection;

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

Route::get('/ami', 'Material\AdMaterialImportController@import');
Route::get('/amia', 'Material\AdMaterialImportController@importA');
Route::get('/amio', 'Material\AdMaterialImportController@importO');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/artisan_reload', function (Request $request) {
    });
});

// Route::get('/event', function (Request $request) {
//     event(new \App\Events\DistributeEvent('Toutiao', 'AuthFailed', [
//         'promote_id' => 1436
//     ]));
//     \Event::fire('user.login', $user);
// });

// Route::get('/test/hprose', function (Request $request) {
//     dispatch(new \App\Jobs\JobHprose('1807'));
// });

// Route::get('/ads', function () {
//     return new AdvertisementCollection(Advertisement::where(['media_id' => 1])->paginate());
// });

Route::get('/helper/parse/ad_material', function () {
    set_time_limit(0);
    AdMaterial::query()
        ->select('id', 'annex_imgs', 'annex_videos', 'annex_video_covers', 'content')
        ->get()
        ->map(function($item) {
            $content = json_decode($item->content)?:[];
            if (sizeof($content) > 0) {
                $annex_imgs = [];
                $annex_videos = [];
                $annex_video_covers = [];
                foreach ($content as $key => $fields) {
                    if (!in_array($key, array('img', 'video'))) {
                        continue;
                    }
                    foreach ($fields as $keyField => $field) {
                        if ($key == 'img') {
                            $annex_imgs[] = $field->id;
                        } elseif ($key == 'video') {
                            if (isset($field->video)) {
                                $annex_videos[] = $field->video->id;
                            }
                            if (isset($field->video_cover)) {
                                $annex_video_covers[] = $field->video_cover->id;
                            }
                        }
                    }
                }
                $item->annex_imgs = implode(',', $annex_imgs);
                $item->annex_videos = implode(',', $annex_videos);
                $item->annex_video_covers = implode(',', $annex_video_covers);
                $item->save();
            }
        });
});