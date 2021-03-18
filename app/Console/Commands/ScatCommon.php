<?php

namespace App\Console\Commands;

use App\Libraries\LibScat;
use App\Models\Advertisement;
use App\Services\PackageSvc;
use Illuminate\Console\Command;

class ScatCommon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scat:common
        {action=nothing : The action name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scat Cron Command description
    | action           | description                                 |
    | ---------------- | ------------------------------------------- |
    | sync_game        | 迁移游戏表数据                               |
    | ad_repkg         | 广告管理 批量更新                            |
    ';

    /**
     * The command action.
     *
     * @var string
     */
    protected $action;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->action = $this->argument('action');

        switch ($this->action) {
            case 'sync_game':
                $this->syncGame();
                break;
            case 'ad_repkg':
                /**
                 * 广告管理 批量更新
                 */
                $query = Advertisement::search_build([
                    'tab_type' => 'yes',
                ])->limit(100);
                $items = collect([]);
                $round = 0;
                do {
                    $round++;
                    $items = $query->get();
                    PackageSvc::repkg($items->pluck('ad_id')->toArray());
                } while ($items->count() > 0 && $round < 10);
                break;
            default:
                break;
        }
    }

    /**
     * 同步母后台的游戏数据到本地
     */
    private function syncGame()
    {
        LibScat::pullGameList();
        LibScat::pullGameGroupList();
    }
}
