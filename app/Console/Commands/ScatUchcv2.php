<?php

namespace App\Console\Commands;

use App\Libraries\LibUchcv2;

use App\Models\Uchcv2AdGroup;

// 账号
use App\Models\UchcAccount;
use App\Models\Uchcv2Account;

// 模板
use App\Models\UchcTpl;
use App\Models\Uchcv2Tpl;

// 文件
use App\Models\UchcFile;
use App\Models\Uchcv2File;

// 广告
use App\Models\Uchc;
use App\Models\Uchcv2;


use Illuminate\Console\Command;

class ScatUchcv2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scat:uchcv2
        {action=all : The action name}
        {--value=3 : The value of the action}
        {--scope= : The scope of the transfer}';

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
                    LibUchcv2::syncAllCreativeReport(['subDays' => $i]);
                }
                break;
            case 'cronEveryFiveMinutes':
                // 同步 UC汇川（UC头条）状态
                LibUchcv2::syncAllCampaignStatus();
                // LibUchcv2::syncAllAdgroupStatus();
                LibUchcv2::syncAllCreativeReport();
                LibUchcv2::syncAllCreativeStatus();
                break;
            case 'cronDailyAt_00_01':
                // 同步 UC汇川（UC头条） 前一天 创意报表
                LibUchcv2::syncAllCreativeReport(['subDays' => 1]);
                break;
            case 'getCreativeTpls':
                $this->getCreativeTpls();
                break;
            case 'transferV1toV2':
                $this->transferV1toV2();
                break;
            default:
                break;
        }
    }

    private function getCreativeTpls()
    {
        $adGroup = Uchcv2AdGroup::query()
            ->with('uchcv2')
            ->where('ad_id', $this->option('value'))
            ->first();
        if (!$adGroup) {
            return;
        }
        $sdk = new \App\Libraries\SdkUchcv2(true);
        $sdk->loadByPromote($adGroup->uchcv2->promote_id);
        $res = $sdk->getCreativeTemplates(2690082);
        echo json_encode($res);
    }

    private function transferV1toV2()
    {
        $scope = $this->option('scope') ?: 'all';
        $fillDates = function ($fake, $item) {
            $fake->created_at = $item->created_at;
            $fake->updated_at = $item->updated_at;
            $fake->deleted_at = $item->deleted_at;
            $fake->save();
            return $fake;
        };

        // 账号
        if ($scope == 'all' || $scope == 'account') {
            UchcAccount::withTrashed()
                ->get()
                ->map(function ($item) use($fillDates) {
                    $fake = Uchcv2Account::query()
                        ->withTrashed()
                        ->updateOrCreate($item->only(['promote_id', 'created_at']), $item->toArray());
                    $fake = $fillDates($fake, $item);
                });
        }

        // 模板
        if ($scope == 'all' || $scope == 'tpl') {
            UchcTpl::withTrashed()
                ->get()
                ->map(function ($item) use($fillDates) {
                    $fake = Uchcv2Tpl::query()
                        ->withTrashed()
                        ->updateOrCreate($item->only(['title', 'created_at']), array_merge([
                            'title' => $item->title,
                            'budget' => $item->budget,
                            'delivery' => $item->rate,
                            'scheduleTime' => $item->schedule_time,
                            'allRegion' => $item->all_region,
                            'region' => $item->region,
                            'gender' => $item->gender,
                            'age' => $item->age,
                            'interest' => $item->interest,
                            'word' => $item->word,
                            'platform' => $item->platform,
                            'networkEnv' => $item->network_env,
                            'intelliTargeting' => $item->intelli_targeting,
                            'creativeSource' => $item->creativeSource,
                        ], $item->only(['created_at', 'updated_at', 'deleted_at'])));
                    $fake = $fillDates($fake, $item);
                });
        }

        // 文件
        if ($scope == 'all' || $scope == 'file') {
            UchcFile::withTrashed()
                ->get()
                ->map(function ($item) use($fillDates) {
                    $fake = Uchcv2File::query()
                        ->withTrashed()
                        ->updateOrCreate($item->only(['promote_id', 'file_id', 'created_at']), $item->toArray());
                    $fake = $fillDates($fake, $item);
                });
        }

        // 广告
        if ($scope == 'all' || $scope == 'ad') {
            Uchc::withTrashed()
                ->with(['ad', 'campaign', 'adgroup', 'creative'])
                ->get()
                ->map(function ($item) use($fillDates) {
                    $fake = Uchcv2::query()
                        ->withTrashed()
                        ->updateOrCreate($item->only(['ad_id', 'promote_id', 'created_at']), $item->toArray());
                    $fake = $fillDates($fake, $item);

                    // 推广计划
                    // TODO: 通过队列，从第三方同步数据
                    if ($item->campaign) {
                        $campaign = $fake->campaign()->updateOrCreate(
                            $item->campaign->only(['ad_id', 'created_at']),
                            array_merge($item->campaign->only(['campaignId']), [
                                'name' => $item->campaign->campaignName
                            ])
                        );
                        $campaign = $fillDates($campaign, $item->campaign);
                        dispatch(new \App\Jobs\JobUchcv2('PULL_CAMPAIGN', [
                            'ad_id' => $item->ad_id,
                            'pulls' => ['AdGroup', 'Creative']
                        ]));
                    }
                });
        }
    }
}
