<?php

namespace App\Jobs;

use App\Exceptions\JobException as Exception;
use App\Libraries\BLogger;
use Illuminate\Support\Facades\Storage;

class JobMaterial extends Job
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'material';

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * 请求动作.
     *
     * @var String
     */
    protected $action;

    /**
     * 请求传参.
     *
     * @var Array
     */
    protected $params = [];

    /**
     * 过程数据存放点.
     *
     * @var Object
     */
    protected $data;

    /**
     * 创建一个新的素材相关的 job 实列，需要严格按照 uses 中的示例使用并传参。
     *
     * @param  String $action 可选值: SYNC_ES_ITEM
     * @param  Object $params
     *
     * @return void
     *
     * @uses __construct('SYNC_ES_ITEM', ['ad_materail_id' => 0]) 同步广告素材信息
     *
     */
    public function __construct($action, $params, $data = null)
    {
        BLogger::scope(['material', 'job'])->info(__FUNCTION__, [$action, $params]);
        $this->action = $action;
        $this->params = $params;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->handleInit();
        switch ($this->action) {
            case 'SYNC_ES_ITEM':
                $this->syncEsItem();
                break;
            case 'EMPTY':
                break;
            default:
                BLogger::scope(['material', 'job'])->warning('该任务不再执行范围内未查询到', $this->descMe());
                break;
        }
    }

    /**
     * 初始化参数、变量
     *
     * @return void
     */
    public function handleInit()
    {
    }

    /**
     * 素材变更时同步
     *
     * @return void
     */
    public function syncEsItem()
    {
        if (isset($this->params['ad_materail_id'])) {
            ad_material_syn($this->params['ad_materail_id']);
            ad_material_annex_syn($this->params['ad_materail_id']);
        } else {
            BLogger::scope(['material', 'job'])->warning(__FUNCTION__, $this->descMe());
        }
    }

    /**
     * The job failed to process.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function failed(\Throwable $e)
    {
        $this->handleInit();
        $zone = $this->action;
        if ($this->params && isset($this->params['zone']) && $this->params['zone']) {
            $zone = $this->params['zone'];
        }
    }

    /**
     * 简要描述当前 job 的内容.
     *
     * @return Array
     */
    protected function descMe()
    {
        return ['action' => $this->action, 'params' => $this->params];
    }
}
