<?php

namespace Tests\Unit\Uchc;

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
        $sdk = new \App\Libraries\SdkUchc(true);
        $sdk->debug = true;

        // 报表
        // $sdk->getReport();
        // $sdk->getTaskState(57657443);
        // $sdk->download(57657303);

        // $sdk->getAccount();

        // $res = $sdk->getCreativeTemplates([31211119]);
        // $res = $sdk->getProvince();
        // $res = $sdk->getConvertType(); // 获取转化监测类型列表
        // $res = $sdk->getAdConvert(); // 根据转化监测类型获取联调通过的转化列表
        // $res = $sdk->getAdgroupByAdgroupId([31231334]);

        // 上传视频
        // $storage = Storage::disk(MaterialAnnexs::$storage_name);
        // $annex = MaterialAnnexs::find(743);
        // $file_path = $storage->path($annex->file_path);
        // // dd($file_path);
        // $sdk->uploadVideo($file_path);
        // $sdk->getVideo();

        // 保存用户信息
        // $sdk->loadByPromote(1193);
        // $sdk->saveAccount();

        $this->assertTrue(true);
    }
}
