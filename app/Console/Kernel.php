<?php

namespace App\Console;

use App\Libraries\LibToutiao;
use App\Libraries\LibUchc;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('clear:log')->dailyAt('02:01');

        // 同步游戏数据
        $schedule->command('scat:common sync_game')->cron('*/5 * * * *');
        $schedule->call(function () {
            $lasttime = time();
            $gameList = gmInterface('promote.game_list', ['source'=>'ad']);
            foreach ($gameList as $key => $value) {
                $value->lasttime = $lasttime;
                if (!$value->alike_game) {
                    $value->alike_game = $value->game_id;
                }
                $game = \App\Models\Game::find($value->game_id);
                if ($game) {
                    $game->where('game_id', $value->game_id)->update(object2array($value));
                } else {
                    \App\Models\Game::query()->insert(object2array($value));
                }
            }

            \App\Models\Game::where('lasttime', '<', $lasttime)->delete();
        })->everyFiveMinutes();

        $schedule->call(function () {
            $models = ['\App\Models\Agent', '\App\Models\Promote', '\App\Models\Media'];
            foreach ($models as $model) {
                // 获取最后更新时间
                $update_time = '0';
                $last = $model::orderBy("update_time", "desc")->first();
                if ($last && $last->update_time) {
                    $update_time = $last->update_time;
                }

                // 获取对应表名
                $tmp = explode('\\', $model);
                $table = strtolower(end($tmp));
                $sync = gmInterface('promote.data_sync', ['table'=>$table, 'update_time'=>$update_time]);
                
                if ($sync === false) {
                    continue;
                }

                $key = "{$table}_id";

                if (!empty($sync->updates)) {
                    foreach ($sync->updates as $k => $val) {
                        $info = $model::find($val->$key);
                        $val->update_time = date('Y-m-d H:i:s');
                        if ($info) {
                            $info->update(object2array($val));
                        } else {
                            $model::create(object2array($val));
                        }
                    }
                }
            }
        })->everyFiveMinutes();

        // 今日头条
        $schedule->command('scat:toutiao cronDailyAtEarlyMorning')->dailyAt('02:01');
        $schedule->command('scat:toutiao cronEveryFiveMinutes')->everyFiveMinutes();

        // UC汇川
        $schedule->command('scat:uchc cronDailyAtEarlyMorning')->dailyAt('09:31');
        $schedule->command('scat:uchc cronEveryFiveMinutes')->everyFiveMinutes();

        /**
         * 定时批量打包
         */
        // $schedule->command('scat:common ad_repkg')->cron('5 */2 * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
