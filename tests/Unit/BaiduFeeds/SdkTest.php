<?php

namespace Tests\Unit\BaiduFeeds;

use App\Libraries\BLogger;
use App\Libraries\SdkBaiduFeeds;

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
        SdkBaiduFeeds::addCreativeFeed();
        $this->assertTrue(true);
    }
}
