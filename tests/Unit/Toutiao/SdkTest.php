<?php

namespace Tests\Unit\Toutiao;

use App\Libraries\BLogger;
use App\Libraries\SdkToutiao;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SdkTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $sdk = new \App\Libraries\SdkToutiao();
        $sdk->loadByPromote(1461);
        // $sdk->debug = true;

        // $sdk->getCustomAudienceList();

        // 查询创意数据
        BLogger::scope(['toutiao', 'sdk'])->debug(
            'getCreativeReport',
            $sdk->getCreativeReport(
                date("Y-m-d", strtotime("-30 day")),
                date('Y-m-d', time()),
                [1609490872570932, 1609490872570884]
            )
        );

        // 转化ID
        // BLogger::scope(['toutiao', 'sdk'])->debug('testExample', $sdk->toolsAdvConvertSelect([
        //     'page' => 2
        //     // 'package_name' => 'com.bie.cjnyx.guaimao'
        // ]));

        // 广告组
        // BLogger::scope(['toutiao', 'sdk'])->debug('testExample', $sdk->campaignGet());

        // 广告计划
        // BLogger::scope(['toutiao', 'sdk'])->debug('testExample', $sdk->adGet([
        //     'advertiser_id' => '93875119979',
        //     'filtering' => json_encode([
        //         'campaign_id' => 62254829258
        //     ])
        // ]));

        // 读取广告创意
        // BLogger::scope(['toutiao', 'sdk'])->debug('testExample', $sdk->creativeRead([
        //     'advertiser_id' => '93875119979',
        //     'ad_id' => '1607498811140173'
        // ]));

        // 生成授权请求链接
        // BLogger::scope(['toutiao', 'sdk'])->debug('testExample', SdkToutiao::getAuthUrlByPromote(1436));

        // 获取 AccessTokenAccessToken
        // $stateStr = "{\"type\":\"promote\",\"id\":1436}";
        // BLogger::scope(['toutiao', 'sdk'])->debug('getAccessTokenWithRequest', json_decode(urldecode($stateStr)));
        $this->assertTrue(true);
    }
}
