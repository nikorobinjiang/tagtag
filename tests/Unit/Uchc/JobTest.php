<?php

namespace Tests\Unit\Uchc;

use App\Libraries\SdkUchc;
use App\Libraries\BLogger;

use App\Models\UchcDwCreative;

use Carbon\Carbon;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class JobTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        // 报表
        // dispatch(new \App\Jobs\JobUchc('PULL_MY_REPORT', [
        //     'promote_id' => 1193,
        //     'body' => [
        //         'startDate' => '2018-10-19',
        //         'endDate' => '2018-10-19',
        //     ]
        // ]));

        // 报表
        // dispatch(new \App\Jobs\JobUchc('PULL_MY_REPORT', [
        //     "promote_id" => 1193,
        //     "body" => [
        //         "startDate" => "2018-12-13",
        //         "endDate" => "2018-12-13",
        //         "idOnly" => false,
        //         "format" => 2,
        //         "reportType" => 4,
        //         "unitOfTime" => 2,
        //         "taskId" => 60833273,
        //         "success" => false
        //     ],
        //     "phase" => "fetch_creative",
        //     "taskId" => 60833273,
        //     "taskInfo" => [
        //         "taskId" => 60833273,
        //         "userId" => 207388058,
        //         "fileId" => 60833133,
        //         "createTime" => "2018-12-13 18:52:36",
        //         "completeTime" => "2018-12-13 18:52:37",
        //         "success" => true
        //     ],
        //     "report" => "sdk_uchc/creative/102415574_102415729_210021791.csv"
        // ]));

        // 推送 广告组
        // dispatch(new \App\Jobs\JobUchc('PUSH_AD', ['ad_id' => 751]));
        // dispatch(new \App\Jobs\JobUchc('PUSH_ADGROUP', ['ad_id' => 687]));
        // dispatch(new \App\Jobs\JobUchc('PUSH_CREATIVE', ['ad_id' => 687]));

        // dispatch(new \App\Jobs\JobUchc('PUSH_CAMPAIGN_STATUS', [
        //     'promote_id' => 1193,
        //     'campaign_ids' => [30731128],
        //     'paused' => true,
        // ]));

        // dispatch(new \App\Jobs\JobUchc('PUSH_CAMPAIGN_BUDGET', [
        //     'promote_id' => 1193,
        //     'campaignIds' => [30697118],
        //     'budget' => 100,
        // ]));

        // dispatch(new \App\Jobs\JobUchc('PUSH_ADGROUP_BID', [
        //     'promote_id' => 1193,
        //     'ad_ids' => [387],
        //     'adgroupIds' => [31248629],
        //     'bidType' => [
        //         'bid' => '0.5',
        //         'bidStage' => 1
        //     ],
        // ]));
        
        // dispatch(new \App\Jobs\JobUchc('PULL_MY_CAMPAIGN_STATUS', [
        //     'promote_id' => 1193
        // ]));
        // dispatch(new \App\Jobs\JobUchc('PULL_MY_CREATIVE_STATUS', [
        //     'promote_id' => 1193
        // ]));
        

        $this->assertTrue(true);
    }
}
