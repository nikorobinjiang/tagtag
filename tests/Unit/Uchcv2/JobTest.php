<?php

namespace Tests\Unit\Uchcv2;

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
        dispatch(new \App\Jobs\JobUchcv2('PULL_MY_REPORT', [
            'promote_id' => 2233,
            'body' => [
                'startDate' => '2019-03-01',
                'endDate' => '2019-03-31',
            ]
        ]));

        // dispatch(new \App\Jobs\JobUchcv2('PULL_MY_REPORT', [
        //     "promote_id" => 2233,
        //     "body" => [
        //         "startDate" => "2019-03-01",
        //         "endDate" => "2019-03-31",
        //         "unitOfTime" => 1,
        //         "taskId" => 610
        //     ],
        //     "phase" => "fetch_creative",
        //     "reportType" => "creative",
        //     "taskId" => 610,
        //     "taskInfo" => [
        //         "reportFileType" => [
        //             "taskId" => 610,
        //             "ready" => true
        //         ]
        //     ],
        //     "report" => "sdk_uchcv2\\creative\\2233_610.csv"
        // ]));

        // 报表
        // dispatch(new \App\Jobs\JobUchcv2('PULL_MY_REPORT', [
        //     "promote_id" => 1193,
        //     "body" => [
        //         "startDate" => "2017-10-19",
        //         "endDate" => "2019-03-01",
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
        //         "createTime" => "2018-09-13 18:52:36",
        //         "completeTime" => "2018-09-13 18:52:37",
        //         "success" => true
        //     ],
        //     "report" => "sdk_uchc/creative/73487452_73487592_207388058.csv"
        // ]));

        // 推送 广告组
        // dispatch(new \App\Jobs\JobUchcv2('PUSH_AD', ['ad_id' => 33137]));
        dispatch(new \App\Jobs\JobUchcv2('PUSH_ADGROUP', ['ad_id' => 33147]));
        // dispatch(new \App\Jobs\JobUchcv2('PUSH_CAMPAIGN', ['ad_id' => 33137]));
        // dispatch(new \App\Jobs\JobUchcv2('PUSH_CREATIVE', ['ad_id' => 33137]));

        // dispatch(new \App\Jobs\JobUchcv2('PULL_CAMPAIGN', [
        //     'ad_id' => 33137,
        //     'pulls' => ['AdGroup', 'Creative']
        // ]));

        // dispatch(new \App\Jobs\JobUchcv2('PULL_CREATIVE', [
        //     'ad_id' => 33137,
        //     'pulls' => ['AdGroup', 'Campaign']
        // ]));

        // dispatch(new \App\Jobs\JobUchcv2('PUSH_CAMPAIGN_STATUS', [
        //     'campaign_id' => 2,
        //     'paused' => true,
        // ]));

        // dispatch(new \App\Jobs\JobUchcv2('PUSH_CAMPAIGN_BUDGET', [
        //     'promote_id' => 1193,
        //     'campaignIds' => [30697118],
        //     'budget' => 100,
        // ]));

        // dispatch(new \App\Jobs\JobUchcv2('PUSH_ADGROUP_BID', [
        //     'promote_id' => 1193,
        //     'ad_ids' => [387],
        //     'adgroupIds' => [31248629],
        //     'bidType' => [
        //         'bid' => '0.5',
        //         'bidStage' => 1
        //     ],
        // ]));
        
        // dispatch(new \App\Jobs\JobUchcv2('PULL_MY_CAMPAIGN_STATUS', [
        //     'promote_id' => 1193
        // ]));
        // dispatch(new \App\Jobs\JobUchcv2('PULL_MY_CREATIVE_STATUS', [
        //     'promote_id' => 1193
        // ]));
        

        $this->assertTrue(true);
    }
}
