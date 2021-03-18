<?php

namespace Tests\Feature;

use App\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaterialReportV2Test extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $user = User::query()->find(40);
        $response = $this->actingAs($user, 'web')->json('GET', '/material/material_report_v2');

        file_put_contents('tmp.json', $response->content());

        $response->assertStatus(200);
    }
}
