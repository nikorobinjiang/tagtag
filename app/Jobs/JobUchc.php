<?php

namespace App\Jobs;

use App\Libraries\BLogger;
use App\Libraries\SdkUchc;

use App\Models\AdDwMaterial;
use App\Models\Advertisement;
use App\Models\AdMaterial;
use App\Models\MaterialAnnexs;
use App\Models\Promote;

use App\Models\Uchc;
use App\Models\UchcCampaign;
use App\Models\UchcAdgroup;
use App\Models\UchcCreative;
use App\Models\UchcFile;
use App\Models\UchcDwCreative;

use App\Exceptions\JobException as Exception;
use App\Exceptions\DistSdkException;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class JobUchc extends Job
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'uchc';

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
     * 针对当前会话请求的客户端.
     *
     * @var \App\Libraries\SdkUchc
     */
    protected $sdk;

    /**
     * 广告主ID.
     *
     * @var Number
     */
    protected $advertiser_id = 0;

    /**
     * 广告组ID.
     *
     * @var String
     */
    protected $campaign_id;

    /**
     * 本地广告组.
     *
     * @var \App\Models\UchcCampaign
     */
    protected $campaign;

    /**
     * 本地广告组.
     *
     * @var \App\Models\UchcAdgroup
     */
    protected $adgroup;

    /**
     * 当前操作的广告.
     *
     * @var \App\Models\Advertisement
     */
    protected $ad;

    /**
     * 第三方账号.
     *
     * @var \App\Models\UchcAccount
     */
    protected $account;

    /**
     * 媒体账号.
     *
     * @var \App\Models\Promote
     */
    protected $promote;

    /**
     * 当前操作的素材信息(广告创意).
     *
     * @var \App\Models\AdMaterial
     */
    protected $materials;

    /**
     * 当前操作的分发平台对象.
     *
     * @var \App\Models\Uchc
     */
    protected $uchc;

    /**
     * 过程数据存放点.
     *
     * @var Object
     */
    protected $data;

    /**
     * 创建一个新的分发相关的 job 实列，需要严格按照 uses 中的示例使用并传参。
     *
     * @param  String $action 可选值: PUSH_CAMPAIGN | PULL_MY_GROUPS | PULL_MY_ADGROUP_STATUS| PUSH_AD
     * @param  Object $params
     *
     * @return void
     *
     * @uses __construct('PUSH_CAMPAIGN', ['group_id' => 0]) 推送广告组
     * @uses __construct('PULL_MY_GROUPS', ['promote_id' => 0]) 拉取对应媒体账号的广告组
     * @uses __construct('PULL_MY_ADGROUP_STATUS', ['promote_id' => 0]) 拉取广告主的所有计划状态
     * @uses __construct('PULL_MY_ADGROUP_STATUS', ['account_id' => 0]) 拉取广告主的所有计划状态
     * @uses __construct('PUSH_AD', ['ad_id' => 0]) 推送广告计划及其创意
     *
     */
    public function __construct($action, $params, $data = null)
    {
        BLogger::scope(['uchc', 'job'])->info(__FUNCTION__, [$action, $params]);
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
            case 'PUSH_CAMPAIGN':
                $this->pushCampaign();
                break;
            case 'PUSH_CAMPAIGN_STATUS':
                $this->pushCampaignStatus();
                break;
            case 'PULL_MY_CAMPAIGN_STATUS':
                $this->pullMyCampaignStatus();
                break;
            case 'PUSH_CAMPAIGN_BUDGET':
                $this->pushCampaignBudget();
                break;
            case 'PUSH_ADGROUP':
                $this->pushAdgroup();
                break;
            case 'PULL_ADGROUP':
                $this->pullAdgroup();
                break;
            case 'PULL_MY_ADGROUP_STATUS':
                $this->pullMyAdgroupStatus();
                break;
            case 'PUSH_ADGROUP_BID':
                $this->pushAdgroupBid();
                break;
            case 'PUSH_CREATIVE':
                $this->pushCreative();
                break;
            case 'PULL_MY_CREATIVE_STATUS':
                $this->pullMyCreativeStatus();
                break;
            case 'PUSH_AD_START':
                $this->pushAdStart();
                break;
            case 'PUSH_AD_END':
                $this->pushAdEnd();
                break;
            case 'PUSH_AD':
                $this->params['zone'] = 'PUSH_AD';
                self::withChain([
                    new self('PUSH_AD_START', $this->params),
                    new self('PUSH_CAMPAIGN', $this->params),
                    new self('PUSH_ADGROUP', $this->params),
                    new self('PUSH_CREATIVE', $this->params),
                    new self('PUSH_AD_END', $this->params),
                ])
                    ->dispatch('EMPTY', $this->params);
                break;
            case 'PULL_MY_REPORT':
                $this->pullMyReport();
                break;
            case 'EMPTY':
                break;
            default:
                BLogger::scope(['uchc', 'job'])->warning('该任务不再执行范围内未查询到', $this->descMe());
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
        $this->sdk = new SdkUchc();

        if (isset($this->params['ad_id'])) {
            $this->ad = Advertisement::find($this->params['ad_id']);
            if ($this->ad) {
                $this->materials = $this->ad->materials;
                $this->uchc = $this->ad->uchc;
                $this->promote = $this->ad->promote;
            }
            if ($this->uchc) {
                $this->campaign = $this->uchc->campaign;
                $this->adgroup = $this->uchc->adgroup;
            } else {
                $this->ad->distribute_sync = 0;
                $this->ad->save();
                BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
                throw new Exception('请填写广告资料');
            }
        }

        if (isset($this->params['promote_id'])) {
            $this->promote = Promote::find($this->params['promote_id']);
        }

        if ($this->promote) {
            $this->account = $this->promote->uchcAccount;
            $this->sdk->loadByPromote($this->promote->promote_id);
        }
    }

    /**
     * 推送广告计划
     *
     * @return void
     */
    public function pushCampaign()
    {
        $campaign = $this->campaign;
        $scheduleTime = str_split($campaign->schedule_time, 24);
        if (sizeof($scheduleTime) != 7) {
            throw new Exception('请重新选择投放时段');
        }
        $campaignType = [
            "campaignName" => $campaign->campaignName,
            "adResourceId" => $campaign->adResourceId,
            "budget" => $campaign->budget,
            "rate" => $campaign->rate,
            "schedule" => [
                "startDate" => intval(Carbon::createFromFormat('Y-m-d H:i:s', $campaign->startDate)->format('Ymd')),
                "endDate" => intval(Carbon::createFromFormat('Y-m-d H:i:s', $campaign->endDate)->format('Ymd')),
                "monday" => $scheduleTime[0],
                "tuesday" => $scheduleTime[1],
                "wednesday" => $scheduleTime[2],
                "thursday" => $scheduleTime[3],
                "friday" => $scheduleTime[4],
                "saturday" => $scheduleTime[5],
                "sunday" => $scheduleTime[6]
            ]
        ];

        $res = null;
        $pushAction = '';
        try {
            if ($campaign->campaignId) {
                $pushAction = 'update';
                $campaignType['campaignId'] = $campaign->campaignId;
                $campaignType['paused'] = $campaign->paused;
                $res = $this->sdk->updateCampaign($campaignType);
            } else {
                $pushAction = 'create';
                $res = $this->sdk->addCampaign($campaignType);
                // if ($res && !array_key_exists('campaignTypes', $res)) {
                //     throw new Exception('计划名'.$campaignType['campaignName'].'未返回');
                // }
            }
        } catch (Exception $e) {
            if (
                $e->getMessage() == '计划名' . $campaignType['campaignName'] . '已经存在'
                || $e->getMessage() == '计划名' . $campaignType['campaignName'] . '未返回'
            ) {
                $adgroupRes = $this->sdk->getAllCampaign();
                if ($adgroupRes && array_key_exists('campaignTypes', $adgroupRes)) {
                    $items = collect($adgroupRes['campaignTypes'])
                        ->where('campaignName', $campaignType['campaignName']);
                    if ($items->count() === 1) {
                        $res = ['campaignTypes' => [
                            $items->first()
                        ]];
                    }
                }
            } else {
                throw $e;
            }
        }

        if ($res && array_key_exists('campaignTypes', $res)) {
            collect($res['campaignTypes'])
                ->each(function ($item) use (&$campaign, &$pushAction) {
                    // 接口返回正确 修改数据库同步状态
                    if ($item['campaignName'] == $campaign->campaignName) {
                        if (isset($item['campaignId']) && $item['campaignId']) {
                            $campaign->campaignId = $item['campaignId'];
                            $campaign->uchc->campaignId = $item['campaignId'];
                        }
                        $campaign->ad_id = $this->ad->ad_id;
                        $campaign->save();
                        $campaign->uchc->save();
                    }
                });
            // 推送广告计划状态
            $this->pushCampaignStatus();
            $this->updateStatus(4, '广告计划');
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception("接口异常");
        }
    }

    /**
     * 推送广告计划状态
     *
     * @return void
     */
    public function pushCampaignStatus()
    {
        $campaignIds = [];
        $paused = null;
        if ($this->ad && $this->campaign) {
            $campaignIds = [$this->campaign->campaignId];
            $paused = $this->ad->status == 1 ? false : true;
        } elseif (
            isset($this->params['paused']) &&
            is_bool($this->params['paused']) &&
            isset($this->params['campaign_ids']) &&
            is_array($this->params['campaign_ids']) &&
            isset($this->params['promote_id']) &&
            $this->params['promote_id'] &&
            $this->sdk->loadByPromote($this->params['promote_id'])
        ) {
            $campaignIds = $this->params['campaign_ids'];
            $paused = $this->params['paused'];
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        $res = $this->sdk->updateCampaignPaused([
            "campaignIds" => $campaignIds,
            "paused" => $paused
        ]);
        if ($res && array_key_exists('campaignIds', $res)) {
            if (sizeof($res['campaignIds']) != sizeof($campaignIds)) {
                BLogger::scope(['uchc', 'job'])->info(__FUNCTION__, $this->descMe());
            } elseif (is_bool($paused)) {
                UchcCampaign::whereIn('campaignId', $res['campaignIds'])
                    ->update(['paused' => $paused]);
            }
            $this->updateStatus(4, '计划状态');
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            if ($res['message'] !== '没有任何修改') {
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
        }
    }

    /**
     * 拉取广告组状态
     *
     * @return void
     */
    public function pullMyCampaignStatus()
    {
        $campaigns = [];
        $campaignIds = [];
        $paused = null;
        if ($this->ad && $this->campaign) {
            $campaignIds = [$this->campaign->campaignId];
            $paused = $this->ad->status == 0 ? true : false;
        } elseif ($this->promote) {
            $ad_ids = [];
            Uchc::where('promote_id', $this->promote->promote_id)
                ->select('ad_id', 'promote_id')
                ->get()
                ->each(function ($item) use (&$ad_ids) {
                    $ad_ids[] = $item->ad_id;
                });
            $campaigns = UchcCampaign::query()
                ->with(['ad' => function ($query) {
                    $query->without(['materials'])
                        ->select('ad_id', 'status', 'distribute_sync', 'distribute_msg');
                }])
                ->select('id', 'ad_id', 'campaignId', 'paused', 'state', 'invalidReasonList')
                ->whereIn('ad_id', $ad_ids)
                ->where('campaignId', "<>", '0')
                ->get();
            $campaigns->each(function ($item) use (&$campaignIds) {
                $campaignIds[] = $item->campaignId;
            });
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        $res = null;
        if (sizeof($campaignIds) > 0) {
            $res = $this->sdk->getCampaignByCampaignId($campaignIds);
        }
        if ($res && array_key_exists('campaignTypes', $res)) {
            collect($res['campaignTypes'])->each(function ($item) use (&$campaigns) {
                $campaign = $campaigns->where('campaignId', $item['campaignId'])->first();
                if (!$campaign) {
                    return;
                }
                $campaign->paused = $item['paused'];
                $campaign->state = $item['state'];
                $campaign->invalidReasonList = $item['invalidReasonList'];
                $campaign->save();

                if ($campaign->ad) {
                    $campaign->ad->status = $item['paused'] == false ? 1 : 0;
                    $campaign->ad->save();
                }
            });
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
        }
    }

    /**
     * 推送广告组预算
     *
     * @return void
     */
    public function pushCampaignBudget()
    {
        $res = [];
        $ad_id = null;
        $campaignIds = [];
        $budget = 0;
        if ($this->ad) {
            $budget = isset($this->params['budget']) ? $this->params['budget'] : $this->campaign->budget;
            $campaignIds = [$this->campaign->campaignId];
            $form = [
                'campaignIds' => $campaignIds,
                'budget' => $budget
            ];
            $res = $this->sdk->updateCampaignBudget($form);
        } elseif (isset($this->params['budget']) && isset($this->params['campaignIds']) && is_array($this->params['campaignIds'])) {
            $budget = $this->params['budget'];
            $campaignIds = $this->params['campaignIds'];
            $form = [
                'campaignIds' => $campaignIds,
                'budget' => $budget
            ];
            $res = $this->sdk->updateCampaignBudget($form);
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        if ($res && array_key_exists('campaignIds', $res)) {
            UchcCampaign::query()
                ->whereIn('campaignId', $res['campaignIds'])
                ->update(['budget' => $budget]);
            $this->updateStatus(4, '广告组预算');
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception(__FUNCTION__);
        }
    }

    /**
     * 推送分发计划 开始
     *
     * @return void
     */
    public function pushAdStart()
    {
        // 同步状态：同步中
        $this->ad->distribute_sync = 2;
        $this->ad->save();
    }

    /**
     * 推送分发计划 结束
     *
     * @return void
     */
    public function pushAdEnd()
    {
        // 同步状态：可同步
        if ($this->ad->distribute_sync == 2) {
            $this->ad->distribute_sync = 1;
            $this->ad->distribute_msg = '';
            $this->ad->save();

            if ($this->uchc && $this->campaign && $this->adgroup) {
                $this->uchc->promote_id = $this->ad->promote_id;
                $this->uchc->campaignId = $this->campaign->campaignId;
                $this->uchc->adgroupId = $this->adgroup->adgroupId;
                $this->uchc->save();
            }
        }
    }

    /**
     * 推送分发计划
     *
     * @return void
     */
    public function pushAdgroup()
    {
        $res = null;
        if ($this->adgroup && $this->adgroup->adgroupId) {
            $adgroupType = $this->collectAdgroupData();
            $adgroupType['adgroupId'] = $this->adgroup->adgroupId;
            $res = $this->sdk->updateAdgroup($adgroupType);
        } else {
            $adgroupType = $this->collectAdgroupData();
            try {
                $res = $this->sdk->addAdgroup($adgroupType);
            } catch (Exception $e) {
                if ($e->getMessage() == '单元名重复') {
                    $adgroupRes = $this->sdk->getAdgroupByCampaignId([
                        $adgroupType['campaignId']
                    ]);
                    if ($adgroupRes && array_key_exists('campaignAdgroups', $adgroupRes)) {
                        $campaignAdgroups = collect($adgroupRes['campaignAdgroups'])
                            ->where('campaignId', $adgroupType['campaignId'])
                            ->first();
                        $itemNow = collect($campaignAdgroups['adgroupTypes'])
                            ->where('adgroupName', $adgroupType['adgroupName'])
                            ->first();
                        if ($itemNow) {
                            $res = ['adgroupTypes' => [
                                $itemNow
                            ]];
                        }
                    }
                } else {
                    throw $e;
                }
            }
        }
        if ($res && array_key_exists('adgroupTypes', $res)) {
            collect($res['adgroupTypes'])->each(function ($item) {
                // 接口返回正确 修改数据库同步状态
                if ($item['adgroupName'] == $this->adgroup->adgroupName) {
                    if (isset($item['adgroupId']) && $item['adgroupId']) {
                        $this->adgroup->adgroupId = $item['adgroupId'];
                        $this->adgroup->uchc->adgroupId = $item['adgroupId'];
                    }
                    $this->adgroup->campaignId = $item['campaignId'];
                    $this->adgroup->paused = $item['paused'];
                    $this->adgroup->bidStage = $item['bidStage'];
                    $this->adgroup->state = $item['state'];
                    $this->adgroup->invalidReasonList = $item['invalidReasonList'];
                    $this->adgroup->save();
                    $this->adgroup->uchc->save();
                }
            });
            $this->updateStatus(4, '计划');
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            if ($res['message'] !== '没有任何修改') {
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
        }
    }

    /**
     * 拉取分发计划
     * TODO
     *
     * @return void
     */
    public function pullAdgroup()
    {
        $res = null;

        if ($res && array_key_exists('data', $res)) {
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /**
     * 拉取计划状态
     *
     * @return void
     */
    public function pullMyAdgroupStatus()
    {
        $adgroupIds = [];
        $paused = null;
        if ($this->ad && $this->adgroup) {
            $adgroupIds = [$this->adgroup->adgroupId];
            $paused = $this->ad->status == 0 ? true : false;
        } elseif ($this->promote) {
            Uchc::where('promote_id', $this->promote->promote_id)
                ->get()
                ->each(function ($item) use (&$adgroupIds) {
                    $adgroupIds[] = $item->adgroupId;
                });
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        $res = null;
        if (sizeof($adgroupIds) > 0) {
            $res = $this->sdk->getAdgroupByAdgroupId($adgroupIds);
        }

        if ($res && array_key_exists('adgroupTypes', $res)) {
            collect($res['adgroupTypes'])->each(function ($item) {
                UchcAdgroup::where('adgroupId', $item['adgroupId'])
                    ->update([
                        'bid' => $item['bid'],
                        'secondBid' => $item['secondBid'],
                        'paused' => $item['paused'],
                        'adconvertId' => $item['adconvertId'],
                        'convertMonitorType' => $item['convertMonitorType'],
                        'optimizationTarget' => $item['optimizationTarget'],
                        'bidStage' => $item['bidStage'],
                        'state' => $item['state'],
                        'adResourceId' => $item['adResourceId'],
                        // 'platform' => $item['platform'],
                        'invalidReasonList' => json_encode(
                            is_array($item['invalidReasonList']) ? $item['invalidReasonList'] : [],
                            JSON_UNESCAPED_UNICODE
                        )
                    ]);
                if (is_array($item['invalidReasonList']) && sizeof($item['invalidReasonList']) > 0) {
                    $adgroup = UchcAdgroup::select('ad_id')->where('adgroupId', $item['adgroupId'])->first();
                    Advertisement::select('ad_id')
                        ->where('ad_id', $adgroup->ad_id)
                        ->where('distribute', 'Uchc')
                        ->update([
                            'distribute_sync' => 3,
                            'distribute_msg' => implode('', $item['invalidReasonList']),
                        ]);
                }
            });
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
        }
    }

    /**
     * 推送 计划出价
     *
     * @return void
     */
    public function pushAdgroupBid()
    {
        $res = [];
        $adgroupIds = [];
        $bidStage = null;
        $bid = 0;

        if ($this->adgroup) {
            $bidStage = $this->adgroup->bidStage;
            if ($bidStage == 1) {
                $bid = $this->adgroup->bid;
            } elseif ($bidStage == 2) {
                $bid = $this->adgroup->secondBid;
            }
            $adgroupIds = [$this->adgroup->adgroupId];
            $form = [
                'adgroupIds' => $adgroupIds,
                'bidType' => [
                    'bid' => $bid,
                    'bidStage' => $bidStage,
                ]
            ];
            $res = $this->sdk->updateAdgroupBid($form);
        } elseif (
            isset($this->params['adgroupIds']) &&
            is_array($this->params['adgroupIds']) &&
            isset($this->params['bidType']) &&
            is_array($this->params['bidType']) &&
            isset($this->params['bidType']['bid']) &&
            is_string($this->params['bidType']['bid']) &&
            isset($this->params['bidType']['bidStage']) &&
            is_numeric($this->params['bidType']['bidStage']) &&
            isset($this->params['promote_id']) &&
            $this->params['promote_id'] &&
            $this->sdk->loadByPromote($this->params['promote_id'])
        ) {
            $bid = $this->params['bidType']['bid'];
            $bidStage = $this->params['bidType']['bidStage'];
            $adgroupIds = $this->params['adgroupIds'];
            $form = [
                'adgroupIds' => $adgroupIds,
                'bidType' => [
                    'bid' => $bid,
                    'bidStage' => $bidStage,
                ]
            ];
            $res = $this->sdk->updateAdgroupBid($form);
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        if ($res && array_key_exists('adgroupIds', $res)) {
            if ($bidStage == 1) {
                UchcAdgroup::query()
                    ->whereIn('adgroupId', $res['adgroupIds'])
                    ->update([
                        'bid' => $bid,
                    ]);
            } elseif ($bidStage == 2) {
                UchcAdgroup::query()
                    ->whereIn('adgroupId', $res['adgroupIds'])
                    ->update([
                        'secondBid' => $bid,
                    ]);
            }
            $this->updateStatus(4, '计划出价');
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception(__FUNCTION__);
        }
    }

    /**
     * 收集本地分发广告数据
     *
     * @return Array
     */
    public function collectAdgroupData()
    {
        $adgroup = $this->adgroup;
        $campaign = $this->campaign;
        $promote = $this->ad->promote;

        $formBase = [
            'campaignId' => $campaign ? $campaign->campaignId : '', // string, 广告组ID
            'adgroupName' => $adgroup ? $adgroup->adgroupName : '', // string, 长度限制：最大30个字节，1个中文按2个字节计算
            'generalizeType' => $adgroup ? $adgroup->generalizeType : '',
            'adResourceId' => $campaign ? $campaign->adResourceId : '',
            'platform' => $adgroup ? $adgroup->platform : '',
            'chargeType' => $adgroup ? $adgroup->chargeType : '',
            'bid' => $adgroup ? $adgroup->bid : '',
            'paused' => $adgroup ? $adgroup->paused : '',
            'adconvertId' => $adgroup ? $adgroup->adconvertId : '',
            'convertMonitorType' => $adgroup ? $adgroup->convertMonitorType : '',
            'optimizationTarget' => $adgroup ? $adgroup->optimizationTarget : '',
            // 'targeting' => $adgroup?$adgroup->targeting:'',
            'index' => $adgroup ? $adgroup->index : '',
            // 'unitType' => ($adgroup&&$adgroup->unitType)?$adgroup->unitType:0,
        ];
        if ($formBase['optimizationTarget'] == 3) {
            // 选填(优化目标为转化时，必填)
            $formBase['secondBid'] = $adgroup ? $adgroup->secondBid : '';
        }

        $targeting = [
            'audience' => $adgroup && is_array($adgroup->audience) ? $adgroup->audience : [], // long[] 人群包定向id集合
            'audience_targeting' => $adgroup ? $adgroup->audience_targeting : '', // String 自定义人群定向
            'all_region' => $adgroup ? $adgroup->all_region : '', // String 投放地域定向
            'region' => $adgroup ? $adgroup->region : [], // int[] 地域id集合
            'gender' => $adgroup ? $adgroup->gender : '', // String 性别定向
            'age' => $adgroup ? $adgroup->age : '', // String 年龄定向范围
            'user_targeting' => $adgroup ? $adgroup->user_targeting : '',
            'interest' => $adgroup ? $adgroup->interest : [],
            'word' => $adgroup ? $adgroup->word : [],
            'url' => $adgroup ? $adgroup->url : [],
            'app' => $adgroup ? $adgroup->app : [],
            'appcategory' => $adgroup ? $adgroup->appcategory : [],
            'network_env' => $adgroup ? $adgroup->network_env : '',
            'intelli_targeting' => $adgroup->intelli_targeting,
            // 'installed_filter' => $adgroup?$adgroup->installed_filter:'',
            // 'search_word' => $adgroup?$adgroup->search_word:'',
            // 'auto_search_word' => $adgroup?$adgroup->auto_search_word:'',
        ];

        if ($targeting['all_region'] == 1) {
            $targeting['region'] = [];
        }

        // 用户定向
        if ($targeting['user_targeting'] == '-1') {
            $targeting['interest'] = [];
            $targeting['word'] = [];
        }

        // BLogger::scope(['uchc', 'job'])->debug([$formBase, $targeting]);
        return array_merge($formBase, ['targeting' => $targeting]);
    }

    /**
     * 推送广告创意
     *
     * @return void
     */
    public function pushCreative()
    {
        $creativeTypes = $this->collectCreativeData();

        $allCount = $this->ad->materials()->where('creative_id', '<>', '')->withTrashed()->count();

        $isUpdate = false;
        if ($allCount > 0) {
            $isUpdate = true;
        }

        if (sizeof($creativeTypes) > 0) {
            $res = null;
            if ($isUpdate) {
                $res = $this->sdk->updateCreative($creativeTypes);
            } else {
                $res = $this->sdk->addCreative($creativeTypes);
            }
            // 推送结果
            if ($res && array_key_exists('creativeTypes', $res)) {
                $creativeTypes = collect($res['creativeTypes']);
                if ($creativeTypes->count() == 1) {
                    $creativeType = $creativeTypes->first();
                    $material = $this->ad->materials()->first();
                    $material->creative_id = $creativeType['creativeId'];
                    $material->status = $creativeType['state'];
                    $material->save();
                    BLogger::scope(['uchc', 'job'])->debug(__FUNCTION__, array_merge($creativeType, [
                        'ad_id' => $this->ad->ad_id,
                        'material_id' => $material->id,
                    ]));
                    $this->uchc->creative()->updateOrCreate(
                        [
                            'creativeId' => $creativeType['creativeId']
                        ],
                        array_merge($creativeType, [
                            'ad_id' => $this->ad->ad_id,
                            'material_id' => $material->id,
                        ])
                    );
                } else {
                    // TODO
                    // $creativeTypes->each(function ($item) {
                    //     // 接口返回正确 修改数据库同步状态
                    //     if ($item['campaignName'] == $this->campaign->campaignName) {
                    //         $this->campaign->campaignId = $item['campaignId'];
                    //         $this->campaign->save();
                    //     }
                    // });
                }
                $this->updateStatus(4, '广告创意');
            } else {
                BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
                throw new Exception(__FUNCTION__);
            }
        } else {
            BLogger::scope(['uchc', 'job'])->notice('推送广告创意无', $this->descMe());
        }
    }

    /**
     * 拉取创意状态
     *
     * @return void
     */
    public function pullMyCreativeStatus()
    {
        $creatives = [];
        $creativeIds = [];
        $paused = null;
        if ($this->ad && $this->campaign) {
            $ad_ids = [$this->campaign->campaignId];
            $paused = $this->ad->status == 0 ? true : false;
        } elseif ($this->promote) {
            $ad_ids = [];
            Uchc::where('promote_id', $this->promote->promote_id)
                ->select('ad_id', 'promote_id')
                ->get()
                ->each(function ($item) use (&$ad_ids) {
                    $ad_ids[] = $item->ad_id;
                });
            $creatives = UchcCreative::query()
                ->with(['ad' => function ($query) {
                    $query->without(['materials'])
                        ->select('ad_id', 'distribute_sync', 'distribute_msg');
                }])
                ->whereIn('ad_id', $ad_ids)
                ->select('id', 'ad_id', 'creativeId', 'state', 'invalidReasonList')
                ->get();
            $creatives->each(function ($item) use (&$creativeIds) {
                $creativeIds[] = $item->creativeId;
            });
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        $res = null;
        if (sizeof($creativeIds) > 0) {
            $res = $this->sdk->getCreativeByCreativeId($creativeIds);
        }
        if ($res && array_key_exists('creativeTypes', $res)) {
            collect($res['creativeTypes'])->each(function ($item) use (&$creatives) {
                $creative = $creatives->where('creativeId', $item['creativeId'])->first();
                if (!$creative) {
                    return;
                }
                $invalidReasonStr = implode(
                    '',
                    is_array($item['invalidReasonList']) ? $item['invalidReasonList'] : []
                );
                $creative->state = $item['state'];
                $creative->invalidReasonList = $item['invalidReasonList'];
                $creative->save();

                if ($creative->ad) {
                    if (strlen($invalidReasonStr) > 0) {
                        $creative->ad->distribute_sync = 3;
                        $creative->ad->distribute_msg = $invalidReasonStr;
                        $creative->ad->save();
                    } elseif (
                        $creative->ad->distribute_sync != 4
                        || $creative->ad->distribute_msg != ''
                    ) {
                        $creative->ad->distribute_sync = 4;
                        $creative->ad->distribute_msg = '';
                        $creative->ad->save();
                    }
                }
            });
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
        }
    }

    /**
     * 收集本地分发创意数据
     *
     * @return Array
     */
    public function collectCreativeData()
    {
        $creativeTypes = [];
        $files = collect($this->uploadFiles());

        $this->ad
            ->materials()
            // ->withTrashed()
            ->each(function ($material, $materialKey) use (&$creativeTypes, $files) {
                // print_r($material->uchcCreative); die;
                $style = $material->style;
                $styleInfo = json_decode($style->style_info);
                $creativeItem = [];
                // 创意素材修改
                if ($material->creative_id) {
                    $creativeItem['creativeId'] = intval($material->creative_id);
                }
                $creativeItem = array_merge($creativeItem, [
                    'adgroupId' => $this->adgroup->adgroupId, // number
                    'campaignId' => $this->campaign->campaignId, // string
                    'creativeTemplateId' => intval(trim($style->enumerated_value)), // string, 样式id通过getCreativeTemplate获取
                    'paused' => $material->creative ? $material->creative->paused : false, // 无效
                    // 'wildcardIds' => null, // 选填 词包id
                    // 'state' => '', // 无效
                    // 'videoId' => '', // 视频创意必填 视频id
                    'index' => $materialKey, // 选填 默认0, 用于标识返回失败对象列表
                ]);
                if ($this->ad->track_url && $this->ad->app_type == 'iOS') {
                    $creativeItem['clickMonitorUrl'] = $this->ad->track_url; // string 选填
                }

                $creativeContent = [
                    'source' => $this->uchc->creativeSource
                ];

                // 根据 app 的不同类型传参
                if ($this->ad->app_type == 'iOS' || $this->campaign->platform == '001') {
                    # ios
                    $creativeItem['appId'] = floatval($this->ad->game->appid); // long ios app创意必填 PP应用商店appId必须与计划关联的appId一致
                    if ($this->adgroup->generalizeType == 1 && $this->ad->has_landing_page == 1) { // 打开页面
                        $creativeContent['target_url'] = $this->ad->shell_url;
                    } else { // APP下载
                        $creativeContent['target_url'] = $this->ad->game_download_url;
                    }
                } elseif ($this->ad->app_type == 'Android' || $this->campaign->platform == '010') {
                    # android
                    // $creativeItem['channelApkId'] = ''; // long android app创意必填
                    $creativeContent['target_url'] = $this->ad->shell_url;
                } else {
                    $creativeContent['target_url'] = $this->ad->shell_url;
                }

                $materialContent = collect(json_decode($material->content));
                $materialContent->each(function ($item, $key) use (&$creativeContent, &$creativeItem, $styleInfo, $files) {
                    if ($key == 'text') {
                        collect($item)->each(function ($item, $key) use (&$creativeContent, &$creativeItem) {
                            $creativeContent[$key] = $item->value;
                            if (isset($item->wildcardIds)) {
                                $creativeItem['wildcardIds'] = $item->wildcardIds;
                            }
                        });
                    } elseif ($key == 'img') {
                        collect($item)->each(function ($item, $key) use (&$creativeContent) {
                            $creativeContent[$key] = $item->preview;
                        });
                    } elseif ($key == 'video' && isset($styleInfo->video)) {
                        foreach ($item as $key => $videoItem) {
                            $styleInfoVideo = collect($styleInfo->video)->filter(function ($item) use ($key) {
                                return $item && $item->name == $key;
                            })->first();
                            if (array_key_exists('video', $videoItem)) {
                                $video = $videoItem->video;
                                $file = $files->filter(function ($item) use ($video) {
                                    return $item && $item->file_name == basename($video->file_url);
                                })->first();
                                if ($file) {
                                    $creativeItem['videoId'] = intval($file->file_id);
                                }
                            }
                            if (array_key_exists('video_cover', $videoItem)) {
                                $video_cover = $videoItem->video_cover;
                                $creativeContent[$styleInfoVideo->cover_name] = $video_cover->preview;
                            }
                        }
                    }
                });
                $creativeItem['content'] = json_encode($creativeContent);
                $creativeTypes[] = $creativeItem;
            });

        return $creativeTypes;
    }

    /**
     * 同步文件
     *
     * @return Array
     */
    public function uploadFiles()
    {
        $promote = $this->ad->promote;
        /**
         * @var \Illuminate\Filesystem\FilesystemAdapter
         */
        $storage = Storage::disk(MaterialAnnexs::$storage_name);

        $material_annexs = $this->materials->map(function ($material) {
            return $material->annex_ids;
        })->implode(',');
        $annex_ids = array_unique(explode(',', $material_annexs));
        $annexs = MaterialAnnexs::whereIn('annex_id', $annex_ids)
            ->get(['annex_id', 'file_type', 'file_path'])
            ->filter(function ($item) use ($storage) {
                return $storage->exists($item->file_path);
            })
            ->map(function ($item) use ($storage) {
                $item->file_path = $storage->path($item->file_path);
                // $item->signature = md5_file($item->file_path);
                return $item;
            });

        $files = [];
        $annexs->each(function ($annex) use (&$files) {
            $file = UchcFile::where('promote_id', $this->promote->promote_id)
                ->where('file_name', basename($annex->file_path))
                ->first();
            if ($file) {
                $annex_ids = $file->annex_ids;
                $annex_ids[] = $annex->annex_id;
                $file->annex_ids = array_unique($annex_ids);
                $file->save();
                $files[] = $file;
                // } elseif (in_array($annex->file_type, ['png', 'jpg'])) {
                //     $res = $this->sdk->uploadImg($this->promote->promote_id, $annex->file_path, 'UPLOAD_BY_FILE', $annex->signature);
                //     if ($res && array_key_exists('data', $res)) {
                //         $data = $res['data'];
                //         $file = UchcFile::create([
                //             'type' => 'img',
                //             'annex_ids' => [$annex->annex_id],
                //             'file_id' => $data['id'],
                //             'promote_id' => $this->promote->promote_id,
                //             'url' => $data['url'],
                //             'signature' => $data['signature'],
                //             'data' => $data,
                //         ]);
                //         $file->save();
                //         return $file;
                //     } else {
                //         BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $annex->toArray());
                //         throw new Exception($res['message']?:__FUNCTION__);
                //     }
            } elseif (in_array($annex->file_type, ['mp4', 'mpeg', '3gp', 'avi'])) {
                $res = $this->sdk->uploadVideo($annex->file_path);
                if ($res && array_key_exists('mcVideos', $res)) {
                    $mcVideos = $res['mcVideos'];
                    foreach ($mcVideos as $key => $mcVideo) {
                        $file = UchcFile::updateOrCreate(
                            [
                                'promote_id' => $this->promote->promote_id,
                                'user_id' => $this->account->userId,
                                'file_name' => $mcVideo['videoName']
                            ],
                            [
                                'type' => 'video',
                                'annex_ids' => [$annex->annex_id],
                                'file_id' => $mcVideo['hcVideoId'],
                                'file_url' => $mcVideo['videoUrl'],
                                // 'signature' => $annex->signature,
                                'data' => $mcVideo,
                            ]
                        );
                        $file->save();
                        $files[] = $file;
                    }
                } else {
                    BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $annex->toArray());
                    throw new Exception($res['message'] ?: __FUNCTION__);
                }
            }
        });

        // ->filter(function ($item) {
        //     return $item;
        // })->unique('signature');
        return $files;
    }

    /**
     * 拉取数据报表
     *
     */
    public function pullMyReport()
    {
        if (!isset($this->params['phase'])) {
            $this->params['phase'] = 'report';
        }
        switch ($this->params['phase']) {
            case 'report':
                $res = $this->sdk->getReport($this->params['body']);
                if (isset($res['taskId']) && isset($res['success'])) {
                    self::dispatch(
                        'PULL_MY_REPORT',
                        array_merge($this->params, [
                            'phase' => 'state',
                            'taskId' => $res['taskId'],
                            'body' => $res
                        ])
                    )
                        ->delay(5);
                } else {
                    BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
                }
                break;
            case 'state':
                $taskInfo = $this->sdk->getTaskState($this->params['taskId']);
                if (isset($taskInfo['taskId']) && isset($taskInfo['success'])) {
                    if ($taskInfo['success']) {
                        self::dispatch(
                            'PULL_MY_REPORT',
                            array_merge($this->params, [
                                'phase' => 'download',
                                'taskInfo' => $taskInfo
                            ])
                        )
                            ->delay(5);
                    } else {
                        self::dispatch(
                            'PULL_MY_REPORT',
                            array_merge($this->params, [
                                'phase' => 'state'
                            ])
                        )
                            ->delay(5);
                    }
                } else {
                    BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
                }
                break;
            case 'download':
                $file = $this->sdk->download($this->params['taskInfo']);
                $body = $this->params['body'];
                $taskInfo = $this->params['taskInfo'];
                if ($file) {
                    $storage = SdkUchc::getStorage();
                    $report = implode(
                        DIRECTORY_SEPARATOR,
                        [
                            SdkUchc::$StorageBaseDir,
                            SdkUchc::getReportTypeField($body['reportType']),
                            implode('_', [
                                $taskInfo['fileId'],
                                $taskInfo['taskId'],
                                $taskInfo['userId'],
                            ]) . '.csv'
                        ]
                    );
                    SdkUchc::transCsv($file);
                    $storage->move($file, $report);
                    self::dispatch(
                        'PULL_MY_REPORT',
                        array_merge($this->params, [
                            'phase' => 'fetch_creative',
                            'report' => $report
                        ])
                    )
                        ->delay(10);
                }
                break;
            case 'fetch_creative':
                /**
                 * @var \Illuminate\Filesystem\FilesystemAdapter
                 */
                $storage = SdkUchc::getStorage();
                $report = $this->params['report'];
                // 重试
                $body = isset($this->params['body'])
                    ? $this->params['body']
                    : [];

                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setReadDataOnly(true);
                $reader->setInputEncoding('GB2312');
                $reader->setEnclosure('');
                $spreadsheet = $reader->load($storage->path($report));

                $fields = [
                    "日期" => "dw_date",
                    "帐户id" => "userId",
                    // "账户" => "userName",
                    "计划id" => "campaignId",
                    // "计划" => "campaignName",
                    "单元id" => "adgroupId",
                    // "单元" => "adgroupName",
                    "创意id" => "creativeId",
                    "展现" => "show",
                    "点击" => "click",
                    "消费" => "stat_cost",
                    "点击率" => "click_rate",
                    "平均点击价格" => "avg_click_price",
                    "千次展现价格" => "cpm_price",
                    "下载开始" => "click_start",
                    "下载完成" => "download_finish",
                    "下载完成率" => "download_finish_rate",
                    "转化类型" => "convertMonitorType",
                    "转化数" => "convert",
                    "转化率" => "convert_rate",
                    "转化成本" => "convert_cost"
                ];
                $fieldNames = array_keys($fields);

                $list = collect($spreadsheet->getSheet(0)->toArray());
                $listHead = $list->shift();

                $today = Carbon::today()->startOfDay()->format('Y-m-d');
                // 只针对非当天的拉起进行重试
                $can_retry = isset($body['startDate']) && ($body['startDate'] != $today);
                $max_retry = 10;
                if ($list->count() <= 0 && $can_retry) {
                    // 重试
                    $body = collect($body)
                        ->only('startDate', 'endDate', 'unitOfTime')
                        ->toArray();
                    $retry_count = isset($this->params['retry_count']) ? $this->params['retry_count'] : 0;
                    BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, ["拉取数据为空, 第{$retry_count}次重试。", $body, $this->descMe()]);
                    if (is_array($body) && sizeof($body) > 0 && $retry_count < $max_retry) {
                        self::dispatch(
                            'PULL_MY_REPORT',
                            [
                                'retry_count' => ++$retry_count,
                                'promote_id' => $this->params['promote_id'],
                                'body' => $body
                            ]
                        )
                            ->delay(now()->addMinutes(10));
                    }
                } elseif (sizeof(array_intersect($fieldNames, $listHead)) == sizeof($fieldNames)) {
                    // 根据创意批量查询广告ID
                    $creativeIdPos = array_search('创意id', $listHead);
                    $creativeIds = $list->map(function ($item) use ($creativeIdPos) {
                        return $item[$creativeIdPos];
                    })->unique();

                    $creativeAdKV = [];
                    $creativeMaterialKV = [];
                    UchcCreative::query()
                        ->whereIn('creativeId', $creativeIds)
                        ->select('ad_id', 'creativeId', 'material_id')
                        ->get()
                        ->each(function ($item) use (&$creativeAdKV, &$creativeMaterialKV) {
                            $creativeAdKV[$item->creativeId] = $item->ad_id;
                            $creativeMaterialKV[$item->creativeId] = $item->material_id;
                        });

                    // 插入创意报表
                    $list->each(function ($item) use ($fields, $listHead, $fieldNames, $creativeAdKV, $creativeMaterialKV, $creativeIdPos) {
                        self::transArrayVal($item);
                        if (isset($creativeAdKV[$item[$creativeIdPos]]) && isset($creativeMaterialKV[$item[$creativeIdPos]])) {
                            $report = [];
                            for ($i = 0; $i < sizeof($item); $i++) {
                                $headName = $listHead[$i];
                                if (!in_array($headName, $fieldNames)) {
                                    continue;
                                }
                                if ($headName == '日期') {
                                    $dw_date_tmp = explode('-', $item[$i]);
                                    if (sizeof($dw_date_tmp) > 0) {
                                        $report[$fields[$headName]] = Carbon::createFromFormat('Ymd', $dw_date_tmp[0])->format('Y-m-d');
                                    } else {
                                        $report[$fields[$headName]] = '';
                                    }
                                } else {
                                    $report[$fields[$headName]] = $item[$i];
                                }
                            }
                            if (isset($report['dw_date']) && strlen($report['dw_date']) > 0) {
                                AdDwMaterial::updateOrCreate([
                                    'ad_id' => $creativeAdKV[$report['creativeId']],
                                    'material_id' => $creativeMaterialKV[$report['creativeId']],
                                    'date' => $report['dw_date']
                                ], [
                                    'show' => $report['show'],
                                    'click' => $report['click'],
                                ]);
                                UchcDwCreative::updateOrCreate([
                                    'dw_date' => $report['dw_date'],
                                    'userId' => $report['userId'],
                                    'campaignId' => $report['campaignId'],
                                    'adgroupId' => $report['adgroupId'],
                                    'creativeId' => $report['creativeId'],
                                ], array_merge($report, [
                                    'ad_id' => $creativeAdKV[$report['creativeId']]
                                ]));
                            }
                        }
                    });
                } else {
                    BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, [$this->descMe(), $listHead, $list]);
                }

                // 删除报表文件
                if ($storage->exists($report)) {
                    $storage->delete($report);
                }
                break;
            default:
                break;
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
        $this->updateStatus(3, $e);
    }

    /**
     * 记录运行结果
     *
     * @return void
     */
    private function updateStatus($sync, $e = null)
    {
        // 0物料待完善, 1可同步, 2同步中, 3同步失败，4同步成功
        $msg = "";
        if (in_array($sync, [1, 4])) {
            $msg = (is_string($e) ? '同步时间:' : '同步时间:') . date('Y-m-d H:i:s', time());
        } elseif (is_object($e)) {
            $msg = $e->getMessage();
        } else {
            $msg = $e;
        }

        $zone = $this->action;
        if ($this->params && isset($this->params['zone']) && $this->params['zone']) {
            $zone = $this->params['zone'];
        }
        if ($sync == 3) {
            BLogger::scope(['uchc', 'job'])->error(__FUNCTION__, [$zone, $this->descMe(), $sync, $e]);
        }
        BLogger::scope(['uchc', 'job'])->info(__FUNCTION__, [$zone, $this->descMe(), $sync, $e, $msg]);
        switch ($zone) {
            case 'PULL_MY_GROUPS':
                break;
            case 'PUSH_CAMPAIGN':
            case 'PUSH_CAMPAIGN_BUDGET':
                if ($this->campaign && $this->campaign->distribute_sync != 3) {
                    $this->campaign->distribute_sync = $sync;
                    $this->campaign->distribute_msg = $msg;
                    $this->campaign->save();
                } elseif (
                    isset($this->params['campaignIds']) &&
                    is_array($this->params['campaignIds']) &&
                    sizeof($this->params['campaignIds']) > 0
                ) {
                    UchcCampaign::whereIn('campaignId', $this->params['campaignIds'])
                        ->update([
                            'distribute_sync' => $sync,
                            'distribute_msg' => $msg
                        ]);
                }
                break;
            case 'PUSH_ADGROUP_BID':
                if ($this->adgroup && $this->adgroup->distribute_sync != 3) {
                    $this->adgroup->distribute_sync = $sync;
                    $this->adgroup->distribute_msg = $msg;
                    $this->adgroup->save();
                } elseif (
                    isset($this->params['adgroupIds']) &&
                    is_array($this->params['adgroupIds']) &&
                    sizeof($this->params['adgroupIds']) > 0
                ) {
                    UchcAdgroup::whereIn('adgroupId', $this->params['adgroupIds'])
                        ->update([
                            'distribute_sync' => $sync,
                            'distribute_msg' => $msg
                        ]);
                }
                break;
            case 'PUSH_AD':
                break;
            default:
                break;
        }

        // 同步状态：同步失败
        if ($this->ad && $this->ad->distribute_sync != 3) {
            $this->ad->distribute_sync = $sync;
            $this->ad->distribute_msg = $msg;
            // $this->pullAdgroup(); // 失败回滚计划
            $this->ad->save();
        } elseif (isset($this->params['ad_ids']) && is_array($this->params['ad_ids']) && sizeof($this->params['ad_ids']) > 0) {
            Advertisement::whereIn('ad_id', $this->params['ad_ids'])
                ->update([
                    'distribute_sync' => $sync,
                    'distribute_msg' => $msg
                ]);
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

    public static function transArrayVal(&$item)
    {
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                if (is_numeric($value) && $value === floor($value)) {
                    $item[$key] = floor($value);
                }
            }
        } else {
            return $item;
        }
    }
}
