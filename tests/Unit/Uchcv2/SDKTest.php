<?php

namespace Tests\Unit\Uchcv2;

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
        $sdk = new \App\Libraries\SdkUchcv2(true);
        $sdk->debug = true;

        // 报表
        // $sdk->getReport();
        // $sdk->getTaskState(57657443);
        // $sdk->download(57657303);

        // $res = $sdk->getAccount();

        // $res = $sdk->getCreativeTemplates(2690082);
        // $res = $sdk->getProvince();
        // $res = $sdk->getConvertType(); // 获取转化监测类型列表
        // $res = $sdk->getAdConvert(); // 根据转化监测类型获取联调通过的转化列表
        // $res = $sdk->getAdgroupByAdgroupId([2690082]);
        // $res = $sdk->getCampaignByAdgroupId([2690082]);
        // $res = $sdk->getCampaignByCampaignId([36262634]);
        // $res = $sdk->getCreativeByCreativeId([50004547]);
    
        // 修改广告计划状态
        // $res = $sdk->updateCampaignPaused([36262634], true);

        // 上传
        // $storage = Storage::disk(MaterialAnnexs::$storage_name);
        // $annex = MaterialAnnexs::find(743);
        // $file_path = $storage->path($annex->file_path);
        // $res = $sdk->uploadVideo($file_path);
        // $res = $sdk->getVideo();
        // $res = $sdk->getImage();

        // 保存用户信息
        // $sdk->loadByPromote(1193);
        // $sdk->saveAccount();

        // DMP
        // $res = $sdk->dmpGetAllPackage();

        $this->assertTrue(true);
    }
}
