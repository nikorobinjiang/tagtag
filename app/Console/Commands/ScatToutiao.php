<?php

namespace App\Console\Commands;

use App\Libraries\LibToutiao;

use Illuminate\Console\Command;

class ScatToutiao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scat:toutiao
        {action=all : The action name}
        {--value=3 : The value of the action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '今日头条命令';

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
                    LibToutiao::syncAllPlanReport(['subDays' => $i]);
                }
                break;
            case 'cronEveryFiveMinutes':
                // 同步头条广告组数据，状态
                LibToutiao::syncAllGroups();
                // 同步头条计划状态、计划数据报表
                LibToutiao::syncAllPlanReport();
                LibToutiao::syncAllPlanStatus();
                break;
            case 'cronDailyAtEarlyMorning':
                // 同步今日头条广告主账户余额
                LibToutiao::syncAllAdvertiserFund();
                for ($i=0; $i < $this->option('value'); $i++) {
                    // 今日头条 前一天 创意数据
                    LibToutiao::syncAllCreativeReport(['subDays' => $i]);
                    // 同步头条 前一天 计划数据报表
                    LibToutiao::syncAllPlanReport(['subDays' => $i]);
                }
                break;
            default:
                break;
        }
    }
}
