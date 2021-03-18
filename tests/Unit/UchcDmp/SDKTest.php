<?php

namespace Tests\Unit\UchcDmp;

use App\Models\MaterialAnnexs;

use App\Libraries\BLogger;
use App\Libraries\LibUchc;

use Illuminate\Support\Facades\Storage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SDKTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $sdk = new \App\Libraries\SdkUchcDmp(true);
        $sdk->debug = true;

        // DMP
        $res = $sdk->getAllPackage();

        print_r(json_encode($res));
        $this->assertTrue(true);
    }
}
