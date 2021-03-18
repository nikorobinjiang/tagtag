<?php

namespace Tests\Unit\Toutiao;

use App\Libraries\BLogger;
use App\Libraries\LibToutiao;

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
        LibToutiao::syncAllCreativeReport(['subDays' => 30]);
        $this->assertTrue(true);
    }
}
