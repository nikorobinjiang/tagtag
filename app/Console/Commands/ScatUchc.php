<?php

namespace App\Console\Commands;

use App\Libraries\LibUchc;

use Illuminate\Console\Command;

class ScatUchc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scat:uchc
        {action=all : The action name}
        {--value=3 : The value of the action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'UC汇川命令';

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
        switch ($this->argument('action')) {
            // 补数据
            case 'refetchReport':
                for ($i=0; $i < $this->option('value'); $i++) {
                    LibUchc::syncAllCreativeReport(['subDays' => $i]);
                }
                break;
            case 'cronEveryFiveMinutes':
                // 同步 UC汇川（UC头条）状态
                LibUchc::syncAllCampaignStatus();
                // LibUchc::syncAllAdgroupStatus();
                LibUchc::syncAllCreativeReport();
                LibUchc::syncAllCreativeStatus();
                break;
            case 'cronDailyAtEarlyMorning':
                // 同步 UC汇川（UC头条） 前一天 创意报表
                LibUchc::syncAllCreativeReport(['subDays' => 1]);
                break;
            default:
                break;
        }
    }
}
