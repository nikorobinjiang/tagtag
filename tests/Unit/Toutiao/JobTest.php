<?php

namespace Tests\Unit\Toutiao;

use App\Libraries\BLogger;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        // dispatch(new \App\Jobs\JobToutiao('PULL_MY_FUND', ['promote_id' => 1436]));

        // 拉取远程广告组
        // dispatch(new \App\Jobs\JobToutiao('PULL_MY_GROUPS', ['promote_id' => 1461]));
        
        // dispatch(new \App\Jobs\JobToutiao('PULL_MY_PLAN_STATUS', ['promote_id' => 1436]));

        // dispatch(new \App\Jobs\JobToutiao('PULL_CREATIVE_ID', ['ad_id' => 320]));
        // dispatch(new \App\Jobs\JobToutiao('PUSH_CREATIVE', ['ad_id' => 667]));
        // dispatch(new \App\Jobs\JobToutiao('PUSH_GROUP', ['group_id' => 151]));
        
        // dispatch(new \App\Jobs\JobToutiao('PUSH_GROUP_STATUS', ['group_id' => 10]));
        // dispatch(new \App\Jobs\JobToutiao('PUSH_PLAN_BUDGET', ['promote_id' => 1461, 'data' => [['ad_id' => '1610938494653485', 'budget' => 100], ['ad_id' => '1611188779994136', 'budget' => 100]]]));
        // dispatch(new \App\Jobs\JobToutiao('PUSH_AD', ['ad_id' => 646]));
        // dispatch(new \App\Jobs\JobToutiao('PUSH_GROUP_STATUS', ['group_id' => 142]));

        // BLogger::scope(['toutiao', 'job'])->debug('testExample', floatval(93875119979));
        
        dispatch(new \App\Jobs\JobToutiao('PULL_MY_REPORT', [
            'promote_id' => 1814,
            'start_date' => '2019-01-01',
            'end_date' => '2019-01-31',
        ]));

        $this->assertTrue(true);
    }
}
