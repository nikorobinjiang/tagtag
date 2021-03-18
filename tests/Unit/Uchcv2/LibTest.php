<?php

namespace Tests\Unit\Uchcv2;

use App\Libraries\BLogger;
use App\Libraries\LibUchcv2;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LibTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        // LibUchcv2::syncAllAdgroupStatus();
        // LibUchcv2::syncAllCampaignStatus();
        // LibUchcv2::syncAllCreativeStatus();
        // LibUchcv2::syncAllCreativeReport();
        // LibUchcv2::syncAllCreativeReport(['subDays' => 1]);
        $this->assertTrue(true);
    }
}
