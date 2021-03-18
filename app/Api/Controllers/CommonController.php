<?php

namespace App\Api\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class CommonController extends ApiController
{
    // 获取基础数据 数据聚合接口
    public function getData(Request $request)
    {
        $query = $request->query();
        $cacheKey = implode(':', [__CLASS__, __FUNCTION__, md5(json_encode($query))]);

        $vdata = Cache::remember($cacheKey, 0, function () use ($query) {
            $vdata = [];
            // $vdata['search'] = $query;
            $queryKeys = array_keys($query);

            /**
             * 游戏列表
             *
             * 可选传参:
             *      game_group_id 游戏组
             *      scope 数据范围, 即当前路由相关的标识
             *      with_all 是否包含全部
             */
            if (in_array('gameList', $queryKeys)) {
                $queryCondition = json_decode($query['gameList'], 1);
                $queryIns = Game::query()
                    ->with(['group:group_id,group_name'])
                    ->select([
                        'group_id', 'game_id', 'game_name', 'game_type',
                        // 'app_type', 'app_store', 'region_name',
                    ])
                    // ->where('group_id', '>', 0)
                    // ->where('game_type', '>', 0)
                     ->where('is_sdk', 2);
                    // ->where('if_show', 1);

                if (Arr::has($queryCondition, 'game_group_id') && Arr::get($queryCondition, 'game_group_id') > -1) {
                    $queryIns->where(
                        'group_id',
                        Arr::get($queryCondition, 'game_group_id')
                    );
                }
                if (array_key_exists('scope', $queryCondition)) {
                    /**
                     * @var \App\Models\System\User
                     */
                    $user = Auth::user();
                    if (!$user && config('app.env') == 'local') {
                        $user = User::find(1);
                    }
                    if ($user) {
                        $queryIns->whereNotIn(
                            'game_id',
                            $user->getLimitGames($queryCondition['scope'])
                        );
                    }
                }

                $gameList = $queryIns
                    ->get()
                    ->map(function ($item) {
                        return [
                            'value' => $item->game_id,
                            'label' => $item->game_name,
                            'group' => $item->group_id,
                            'type' => $item->game_type,
                            // 'district' => $item->region_name,
                        ];
                    });
                if (array_key_exists('scope', $queryCondition)) {
                    if ($queryCondition['scope'] == 'report.distToutiao') {
                        $settingGames = AdDataSetting::query()
                            ->select(DB::raw("DISTINCT game_id"))
                            ->get()
                            ->pluck('game_id')
                            ->toArray();
                        $gameList = $gameList
                            ->map(function ($item) use ($settingGames) {
                                $item['has_dist'] = in_array(
                                    $item['value'],
                                    $settingGames
                                );
                                return $item;
                            });
                    }
                }
                if (Arr::get($queryCondition, 'with_all')) {
                    $gameList = collect([
                        ['label' => '全部游戏', 'value' => -1],
                    ])->concat($gameList);
                }
                $vdata['gameList'] = $gameList;
            }
        });

        return $this->success($vdata);
    }
}
