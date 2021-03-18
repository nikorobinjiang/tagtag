<?php

namespace Tests\Unit\Uchc;

use App\Libraries\BLogger;
use App\Libraries\LibUchc;

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
        LibUchc::syncAllCreativeStatus();
        $this->assertTrue(true);
    }
}
