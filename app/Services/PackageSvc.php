<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\Down;
use Illuminate\Support\Facades\DB;

class PackageSvc
{
    /**
     * 批量打包
     */
    public static function repkg(array $ids)
    {
        $query = Advertisement::search_build([])
            ->without(['materials'])
            ->with(['down', 'game'])
            ->where('ww_ad.down_id', '>', 0)
            ->select('ww_ad.ad_id', 'ww_ad.down_id', 'ww_ad.game_id', 'ww_ad.sub_promote_id', 'ww_ad.has_watermark');
        if (!in_array('0', $ids)) {
            $query->whereIn('ww_ad.ad_id', $ids);
        }

        $items = $query->get();
        $data = $cps_data = [];
        foreach ($items as $key => $value) {
            if ($value->game->access_mode == 2) {
                $cps_data[] = $value->down_id;
            } else {
                $data[] = [
                    'game_id' => $value->game_id,
                    'promote_id' => $value->sub_promote_id,
                    'has_watermark' => $value->has_watermark,
                    'source_pkg' => $value->down->source_pkg
                ];
            }
        }

        if (sizeof($cps_data) > 0) {
            Down::leftJoin('ww_game', 'ww_down.game_id', '=', 'ww_game.game_id')
                ->whereIn('ww_down.down_id', $cps_data)
                ->update(['pkg_status' => 1, 'ww_down.down_url' => DB::raw('`ww_game`.`game_download_url`')]);
        }

        if (sizeof($data) > 0) {
            $res = gmInterface('promote.batch_apply', ['data' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
            if ($res) {
                $batchSize = 20;
                $batchWhere = [];
                foreach ($res as $key => $value) {
                    $batchWhere[] =  "(`game_id` = {$value->game_id} AND `promote_id` = {$value->promote_id})";
                    if (($key + 1) % $batchSize === 0) {
                        Down::whereRaw(implode(' OR ', $batchWhere))
                            ->update(['pkg_status' => 0]);
                        $batchWhere = [];
                    }
                    // Down::where('game_id', $value->game_id)
                    //     ->where('promote_id', $value->promote_id)
                    //     ->update(['pkg_status' => 0]);
                    // 完成
                    /*if ($value->apply_status == 2 && $value->down_url) {
                        Down::where('game_id',$value->game_id)
                        ->where('promote_id',$value->promote_id)
                        ->update(['pkg_status'=>1, 'down_url'=>$value->down_url]);
                    } else {
                        Down::where('game_id',$value->game_id)
                        ->where('promote_id',$value->promote_id)
                        ->update(['pkg_status'=>0]);
                    }*/
                }
                if (sizeof($batchWhere) > 0) {
                    Down::whereRaw(implode(' OR ', $batchWhere))
                        ->update(['pkg_status' => 0]);
                }
                return true;
            }
            return false;
        }
        return true;
    }
}
