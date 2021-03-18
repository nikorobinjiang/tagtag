<?php

namespace App\Jobs;

use App\Libraries\SdkUchcv2;

use App\Models\AdDwMaterial;
use App\Models\Advertisement;
use App\Models\AdMaterial;
use App\Models\MaterialAnnexs;
use App\Models\Promote;

use App\Models\Uchcv2;
use App\Models\Uchcv2Campaign;
use App\Models\Uchcv2AdGroup;
use App\Models\Uchcv2Creative;
use App\Models\Uchcv2File;
use App\Models\Uchcv2DwCreative;

use App\Exceptions\JobException as Exception;
use App\Exceptions\DistSdkException;
use App\Libraries\BLogger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class JobUchcv2 extends Job
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'uchcv2';

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
     * @var \App\Libraries\SdkUchcv2
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
    protected $ad_group_id;

    /**
     * 本地广告组.
     *
     * @var \App\Models\Uchcv2AdGroup
     */
    protected $ad_group;

    /**
     * 本地广告组.
     *
     * @var \App\Models\Uchcv2Campaign
     */
    protected $campaign;

    /**
     * 当前操作的广告.
     *
     * @var \App\Models\Advertisement
     */
    protected $ad;

    /**
     * 第三方账号.
     *
     * @var \App\Models\Uchcv2Account
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
     * @var \App\Models\Uchcv2
     */
    protected $uchcv2;

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
        BLogger::scope(['uchcv2', 'job'])->info(__FUNCTION__, [$action, $params]);
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
            case 'PUSH_ADGROUP':
                $this->pushAdGroup();
                break;
            case 'PULL_ADGROUP':
                $this->pullAdGroup();
                break;
            case 'PUSH_ADGROUP_STATUS':
                $this->pushAdGroupStatus();
                break;
            case 'PULL_MY_ADGROUP_STATUS':
                $this->pullMyAdGroupStatus();
                break;
            case 'PUSH_CAMPAIGN':
                $this->pushCampaign();
                break;
            case 'PULL_CAMPAIGN':
                $this->pullCampaign();
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
            case 'PUSH_CREATIVE':
                $this->pushCreative();
                break;
            case 'PULL_CREATIVE':
                $this->pullCreative();
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
                    new self('PUSH_ADGROUP', $this->params),
                    new self('PUSH_CAMPAIGN', $this->params),
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
                BLogger::scope(['uchcv2', 'job'])->warning('该任务不再执行范围内未查询到', $this->descMe());
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
        $this->sdk = new SdkUchcv2();

        if (Arr::has($this->params, 'campaign_id')) {
            $campaign = Uchcv2Campaign::query()
                ->with(['ad', 'uchc'])
                ->find($this->params['campaign_id']);
            if ($campaign) {
                $this->params['ad_id'] = $campaign->ad_id;
            }
        }

        if (isset($this->params['ad_id'])) {
            $this->ad = Advertisement::find($this->params['ad_id']);
            if ($this->ad) {
                $this->materials = $this->ad->materials;
                $this->promote = $this->ad->promote;
                $this->uchcv2 = $this->ad->uchcv2;
            }
            if ($this->uchcv2) {
                $this->adGroup = $this->uchcv2->ad_group;
                $this->campaign = $this->uchcv2->campaign;
            } else {
                $this->ad->distribute_sync = 0;
                $this->ad->save();
                BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
                throw new Exception('请填写广告资料');
            }
        }

        if (Arr::has($this->params, 'promote_id')) {
            $this->promote = Promote::find($this->params['promote_id']);
        }

        if ($this->promote) {
            $this->account = $this->promote->uchcv2Account;
            $this->sdk->loadByPromote($this->promote->promote_id);
        }
    }

    /**
     * 推送推广组
     *
     * @return void
     */
    public function pushAdGroup()
    {
        $res = null;
        $action = 'update';
        $ad_group = $this->adGroup;
        if ($ad_group && $ad_group->adGroupId) {
            $action = 'update';
            $adgroupType = $this->assemAdGroupData();
            $adgroupType['id'] = $ad_group->adGroupId;
            $res = $this->sdk->updateAdGroup($adgroupType);
        } else {
            $action = 'add';
            $adgroupType = $this->assemAdGroupData();
            try {
                $res = $this->sdk->addAdGroup($adgroupType);
            } catch (\Throwable $e) {
                if ($e->countRemoteCodes() == 1 && in_array(9012004, $e->getRemoteCodes())) {
                    $adgroupRes = $this->sdk->getAllAdGroup();
                    if (Arr::has($adgroupRes, 'adGroupTypes')) {
                        $items = collect(Arr::get($adgroupRes, 'adGroupTypes', []))
                            ->where('name', $adgroupType['name']);
                        if ($items->count() === 1) {
                            $item = $items->first();
                            $res = ['adGroupIds' => [
                                Arr::get($item, 'id')
                            ]];
                        }
                    }
                } else {
                    throw $e;
                }
            }
        }

        if (!Arr::has($res, 'adGroupIds')) {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            if ($res['message'] !== '没有任何修改') {
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
            return;
        }

        $adGroups = $this->sdk->getAdGroupByAdGroupId(Arr::get($res, 'adGroupIds', []));
        if (!Arr::has($adGroups, 'adGroupTypes')) {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            if ($res['message'] !== '没有任何修改') {
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
            return;
        }
        collect($adGroups['adGroupTypes'])->each(function ($item) use ($ad_group, $action) {
            // 接口返回正确 修改数据库同步状态
            if ($item['name'] == $ad_group->name) {
                $uchcv2 = $ad_group->uchcv2;
                if (Arr::get($item, 'id', false)) {
                    $ad_group->adGroupId = $item['id'];
                    $uchcv2->adGroupId = $item['id'];
                }
                $ad_group->paused = $item['paused'];
                $ad_group->index = $item['index'];
                $ad_group->save();
                $uchcv2->save();
                if ($action == 'add') {
                    dispatch(new \App\Jobs\JobUchcv2('PUSH_ADGROUP_STATUS', [
                        'ad_id' => $ad_group->ad_id,
                        'paused' => true
                    ]));
                }
            }
        });
        $this->updateStatus(4, '推广组');
    }

    /**
     * 拉取推广组
     *
     * @return void
     */
    public function pullAdGroup()
    {
        $adGroupIds = [];

        if ($this->ad && $this->adGroup) {
            $adGroupIds = [$this->adGroup->adGroupId];
        } elseif (Arr::has($this->params, 'adGroupIds')) {
            $adGroupIds = Arr::get($this->params, 'adGroupIds', null);
            if (!is_array(!$adGroupIds)) {
                $adGroupIds = [$adGroupIds];
            }
        }

        $res = null;
        if (sizeof($adGroupIds) > 0) {
            $res = $this->sdk->getAdGroupByAdGroupId($adGroupIds);
        }

        if (!Arr::has($res, 'adGroupTypes')) {
            BLogger::scope(['uchcv2', 'job'])->error(__FUNCTION__, $this->descMe());
            return;
        }

        collect($res['adGroupTypes'])->each(function ($item) {
            $adGroup = Uchcv2AdGroup::query()
                ->where(['adGroupId' => Arr::get($item, 'id')])
                ->first();
            if (!$adGroup) {
                return;
            }
            $adGroup->fill($item);
            $adGroup->save();
            $adGroup->uchcv2->adGroupId = $adGroup->adGroupId;
            $adGroup->uchcv2->save();
        });
    }

    /**
     * 推送广告组状态
     *
     * @return void
     */
    public function pushAdGroupStatus()
    {
        $adGroupIds = [];
        $paused = null;
        if ($this->ad && $this->adGroup) {
            $adGroupIds = [$this->adGroup->adGroupId];
            if (isset($this->params['paused']) && is_bool($this->params['paused'])) {
                $paused = $this->params['paused'];
            } else {
                $paused = $this->ad->status == 1 ? false : true;
            }
        } elseif (
            isset($this->params['paused']) &&
            is_bool($this->params['paused']) &&
            isset($this->params['ad_group_ids']) &&
            is_array($this->params['ad_group_ids']) &&
            isset($this->params['promote_id']) &&
            $this->params['promote_id'] &&
            $this->sdk->loadByPromote($this->params['promote_id'])
        ) {
            $adGroupIds = $this->params['ad_group_ids'];
            $paused = $this->params['paused'];
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        $res = $this->sdk->updateAdgroupPaused($adGroupIds, $paused);
        if (Arr::has($res, 'adGroupIds')) {
            if (sizeof($res['adGroupIds']) != sizeof($adGroupIds)) {
                BLogger::scope(['uchcv2', 'job'])->info(__FUNCTION__, $this->descMe());
            } elseif (is_bool($paused)) {
                Uchcv2AdGroup::whereIn('adGroupId', $res['adGroupIds'])
                    ->update(['paused' => $paused]);
            }
            $this->updateStatus(4, '广告组状态');
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            if ($res['message'] !== '没有任何修改') {
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
        }
    }

    /**
     * 拉取计划状态
     *
     * @return void
     */
    public function pullMyAdGroupStatus()
    {
        $adGroupIds = [];
        $paused = null;
        if ($this->ad && $this->adGroup) {
            $adGroupIds = [$this->adGroup->adGroupId];
            $paused = $this->ad->status == 0 ? true : false;
        } elseif ($this->promote) {
            $adGroupIds = Uchcv2::where('promote_id', $this->promote->promote_id)
                ->get()
                ->map(function ($item) {
                    return $item->adGroupId;
                })
                ->values();
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        $res = null;
        if (sizeof($adGroupIds) > 0) {
            $res = $this->sdk->getAdGroupByAdGroupId($adGroupIds);
        }

        if (Arr::has($res, 'adgroupTypes')) {
            collect(Arr::get($res, 'adgroupTypes', []))->each(function ($item) {
                Uchcv2AdGroup::query()
                    ->where('adGroupId', $item['adGroupId'])
                    ->update([
                        'name' => $item['name'],
                        'objectiveType' => $item['objectiveType'],
                        'paused' => $item['paused'],
                        'index' => $item['index'],
                    ]);
            });
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
        }
    }

    /**
     * 收集本地分发广告数据
     *
     * @return Array
     */
    public function assemAdGroupData()
    {
        $ad_group = $this->adGroup;
        $promote = $this->ad->promote;

        $formBase = [
            'name' => Arr::get($ad_group, 'name', null), // string, 必填。长度限制：最大50个字符，1个中文按2个字符计算
            'objectiveType' => Arr::get($ad_group, 'objectiveType', -1), // 必填
            'paused' => true,
            'index' => $ad_group->id,
        ];

        return $formBase;
    }

    /**
     * 推送广告计划
     *
     * @return void
     */
    public function pushCampaign()
    {
        $campaign = $this->campaign;
        $scheduleTime = str_split($campaign->scheduleTime, 24);
        if (sizeof($scheduleTime) != 7) {
            throw new Exception('请重新选择投放时段');
        }
        $campaignType = $this->assemCampaignData();

        $res = null;
        $pushAction = '';
        try {
            if ($campaign->campaignId) {
                $pushAction = 'update';
                $campaignType['id'] = $campaign->campaignId;
                $campaignType['paused'] = $campaign->paused;
                $res = $this->sdk->updateCampaign($campaignType);
            } else {
                $pushAction = 'create';
                $res = $this->sdk->addCampaign($campaignType);
                // if ($res && !array_key_exists('campaignTypes', $res)) {
                //     throw new Exception('计划名'.$campaignType['name'].'未返回');
                // }
            }
        } catch (\Throwable $e) {
            if ($e->countRemoteCodes() == 1 && in_array(9014047, $e->getRemoteCodes())) {
                $campaignRes = $this->sdk->getCampaignByAdGroupId($this->uchcv2->adGroupId);
                if (Arr::has($campaignRes, 'adGroupCampaigns')) {
                    $items = collect(Arr::get($campaignRes, 'adGroupCampaigns', []))
                        ->pluck('campaignTypes')
                        ->flatten(1)
                        ->where('name', $campaign['name']);
                    if ($items->count() === 1) {
                        $item = $items->first();
                        $res = [
                            'campaignIds' => [Arr::get($item, 'id')],
                        ];
                    } else {
                        $res = [
                            'message' => '推送广告计划异常',
                        ];
                    }
                }
            } else {
                throw $e;
            }
        }
        if (!Arr::has($res, 'campaignIds')) {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            if ($res['message'] !== '没有任何修改') {
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
            return;
        }

        $campaigns = $this->sdk->getCampaignByCampaignId(Arr::get($res, 'campaignIds', []));
        if (!Arr::has($campaigns, 'campaignTypes')) {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }
        collect($campaigns['campaignTypes'])->each(function ($item) use ($campaign) {
            // 接口返回正确 修改数据库同步状态
            if ($item['index'] == $campaign->id || ($item['name'] == $campaign->name)) {
                if (Arr::get($item, 'id', false)) {
                    $campaign->campaignId = $item['id'];
                    $campaign->uchcv2->campaignId = $item['id'];
                }
                $campaign->objectives = Arr::get($item, 'objectives');
                $campaign->targetings = Arr::get($item, 'targetings');
                $campaign->bids = Arr::get($item, 'bids');
                $campaign->schedule = Arr::get($item, 'schedule');
                $campaign->paused = $item['paused'];
                $campaign->index = $item['index'];
                $campaign->save();
                $campaign->uchcv2->save();
            }
        });
        $this->updateStatus(4, '广告计划');
    }

    /**
     * 拉取推广计划
     *
     * @return void
     */
    public function pullCampaign()
    {
        $campaignIds = [];

        if ($this->ad && $this->campaign) {
            $campaignIds = [$this->campaign->campaignId];
        } elseif (Arr::has($this->params, 'campaignIds')) {
            $campaignIds = Arr::get($this->params, 'campaignIds', null);
            if (!is_array(!$campaignIds)) {
                $campaignIds = [$campaignIds];
            }
        }

        $res = null;
        if (sizeof($campaignIds) > 0) {
            $res = $this->sdk->getCampaignByCampaignId($campaignIds);
        }

        if (!Arr::has($res, 'campaignTypes')) {
            BLogger::scope(['uchcv2', 'job'])->error(__FUNCTION__, $this->descMe());
            return;
        }

        collect($res['campaignTypes'])->each(function ($item) {
            $campaign = Uchcv2Campaign::query()
                ->where(['campaignId' => Arr::get($item, 'id')])
                ->first();
            if (!$campaign) {
                return;
            }
            $campaign->fill($item);
            $campaign->objectives = Arr::get($item, 'objectives');
            $campaign->targetings = Arr::get($item, 'targetings');
            $campaign->bids = Arr::get($item, 'bids');
            $campaign->schedule = Arr::get($item, 'schedule');
            $campaign->save();
            $campaign->uchcv2->campaignId = $campaign->campaignId;
            $campaign->uchcv2->save();


            if (in_array('AdGroup', Arr::get($this->params, 'pulls', []))) {
                dispatch(new \App\Jobs\JobUchcv2('PULL_ADGROUP', [
                    'ad_id' => $campaign->ad_id,
                ]));
            }
            if (in_array('Creative', Arr::get($this->params, 'pulls', []))) {
                dispatch(new \App\Jobs\JobUchcv2('PULL_CREATIVE', [
                    'ad_id' => $campaign->ad_id,
                ]));
            }
        });
    }

    /**
     * 推送推广计划开关
     *
     * @return void
     */
    public function pushCampaignStatus()
    {
        $campaignIds = [];
        $paused = null;
        if ($this->ad && $this->campaign) {
            $campaignIds = [$this->campaign->campaignId];
            $paused = $this->ad->status == 0 ? true : false;
        } elseif ($this->promote) {
            $campaignIds = Uchcv2::where('promote_id', $this->promote->promote_id)
                ->get()
                ->map(function ($item) {
                    return $item->campaignId;
                })
                ->values();
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        $res = null;
        if (sizeof($campaignIds) > 0) {
            $res = $this->sdk->updateCampaignPaused($campaignIds, $paused);
        }

        $campaigns = $this->sdk->getCampaignByCampaignId(Arr::get($res, 'campaignIds', []));
        if (!Arr::has($campaigns, 'campaignTypes')) {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }
        if (Arr::has($res, 'campaignTypes')) {
            collect(Arr::get($res, 'campaignTypes', []))->each(function ($item) {
                Uchcv2Campaign::query()
                    ->where('campaignId', $item['id'])
                    ->update([
                        'paused' => $item['paused'],
                        'index' => $item['index'],
                    ]);
            });
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
        }
    }

    /**
     * 拉取广告计划状态
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
            $ad_ids = Uchcv2::where('promote_id', $this->promote->promote_id)
                ->select('ad_id', 'promote_id')
                ->get()
                ->map(function ($item) {
                    return $item->ad_id;
                });
            $campaigns = Uchcv2Campaign::query()
                ->with(['ad' => function ($query) {
                    $query->without(['materials'])
                        ->select('ad_id', 'status', 'distribute_sync', 'distribute_msg');
                }])
                ->select('id', 'ad_id', 'campaignId', 'paused', 'state')
                ->whereIn('ad_id', $ad_ids)
                ->where('campaignId', "<>", '0')
                ->get();
            $campaignIds = $campaigns->map(function ($item) {
                return $item->campaignId;
            })->toArray();
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        $res = null;
        if (sizeof($campaignIds) > 0) {
            $res = $this->sdk->getCampaignByCampaignId($campaignIds);
        }
        if (Arr::has($res, 'campaignTypes')) {
            collect(Arr::get($res, 'campaignTypes'))->each(function ($item) use (&$campaigns) {
                $campaign = $campaigns->where('campaignId', $item['id'])->first();
                if (!$campaign) {
                    return;
                }
                $campaign->paused = $item['paused'];
                $campaign->state = $item['state'];
                $campaign->save();

                if ($campaign->ad) {
                    $campaign->ad->status = $item['paused'] == false ? 1 : 0;
                    $campaign->ad->save();
                }
            });
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
        }
    }

    /**
     * 推送广告计划预算
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
                'budget' => $budget,
                'forceBudget' => true
            ];
            $res = $this->sdk->updateBudget($form);
        } elseif (isset($this->params['budget']) && isset($this->params['campaignIds']) && is_array($this->params['campaignIds'])) {
            $budget = $this->params['budget'];
            $campaignIds = $this->params['campaignIds'];
            $form = [
                'campaignIds' => $campaignIds,
                'budget' => $budget,
                'forceBudget' => true
            ];
            $res = $this->sdk->updateBudget($form);
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        if (Arr::has($res, 'campaignIds')) {
            Uchcv2Campaign::query()
                ->whereIn('campaignId', $res['campaignIds'])
                ->update(['budget' => $budget]);
            $this->updateStatus(4, '广告组预算');
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception(__FUNCTION__);
        }
    }

    /**
     * 收集本地推广计划数据
     *
     * @return Array
     */
    public function assemCampaignData()
    {
        $campaign = $this->campaign;

        $formBase = [
            'adGroupId'  => $this->adGroup->adGroupId,
            'name'       => Arr::get($campaign, 'name', null), // string, 必填。长度限制：最大50个字符，1个中文按2个字符计算
            'type'       => $this->adGroup ? $this->adGroup->objectiveType : -1, // 必填
            'optTarget'  => Arr::get($campaign, 'optTarget', -1),
            'delivery'   => Arr::get($campaign, 'delivery', -1),
            // 'objectives' => Arr::get($campaign, 'objectives', null), // Objective 推广对象内容
            'trackArgs'  => Arr::get($campaign, 'trackArgs', null),
            'targetings' => Arr::get($campaign, 'targetings', null),
            'budget'     => Arr::get($campaign, 'budget', 0),
            'schedule'   => Arr::get($campaign, 'schedule', null),
            'chargeType' => Arr::get($campaign, 'chargeType', null),
            'bids'       => Arr::get($campaign, 'bids', null),
            'index'      => $campaign->id,
        ];

        $objectives = [
            'targetUrl' => $this->ad->shell_url,
        ];
        // if ($this->ad->track_url) {
        //     $objectives['schemeUrl'] = $this->ad->track_url;
        // }
        if (in_array($campaign->platform, ['001', '010'])) {
            $objectives['appName'] = $this->ad->game->game_name;
            if ($campaign->platform == '001') {
                # ios
                $objectives['appKey'] = 0;
            } elseif ($campaign->platform == '010') {
                # android
                $objectives['packageKey'] = 0;
            }
        }
        // 优化目标 转化
        if ($campaign->optTarget == 3) {
            $objectives['convertType'] = $campaign->convertType;
            $objectives['adConvertId'] = $campaign->adConvertId;
        }
        $formBase['objectives'] = [$objectives];

        $formBase['index'] = $campaign->id;
        return $formBase;
    }

    /**
     * 推送推广组 开始
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
     * 推送推广组 结束
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

            if ($this->uchcv2 && $this->campaign && $this->adGroup) {
                $this->uchcv2->promote_id = $this->ad->promote_id;
                $this->uchcv2->campaignId = $this->campaign->campaignId;
                $this->uchcv2->adGroupId = $this->adGroup->adGroupId;
                $this->uchcv2->save();
            }
        }
    }

    /**
     * 推送广告创意
     * TODO: fix edit creative
     *
     * @return void
     */
    public function pushCreative()
    {
        $creativeTypes = collect($this->assemCreativeData());

        $trashedCreative = $this->ad->materials()->where('creative_id', '<>', '')->onlyTrashed()->get();
        $existCreative = $this->ad->materials()->where('creative_id', '<>', '')->get();

        // 删除，实际操作为暂停
        if ($trashedCreative->count() > 0) {
            try {
                $trashedRes = $this->sdk->updateCreativePaused(
                    $trashedCreative
                        ->pluck('creative_id')
                        ->map(function ($item) {
                            return intval($item);
                        })
                        ->values()->toArray()
                );
            } catch (\Throwable $e) {
                BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            }
        }

        $creativeIdsRes = [];

        // 更新
        $updateList = $creativeTypes->filter(function ($item) {
            return Arr::has($item, 'id');
        })->values();
        if ($updateList->count() > 0) {
            $updateRes = $this->sdk->updateCreative($creativeTypes->toArray());
            if (Arr::has($updateRes, 'creativeIds')) {
                array_push($creativeIdsRes, ...Arr::get($updateRes, 'creativeIds'));
            }
        }

        // 新增
        $createList = $creativeTypes->filter(function ($item) {
            return !Arr::has($item, 'id');
        })->values();
        if ($createList->count() > 0) {
            $createRes = $this->sdk->addCreative($creativeTypes->toArray());
            if (Arr::has($createRes, 'creativeIds')) {
                array_push($creativeIdsRes, ...Arr::get($createRes, 'creativeIds'));
            }
        }

        if (count($creativeIdsRes) > 0) {
            $creatives = $this->sdk->getCreativeByCreativeId($creativeIdsRes);
            if (!Arr::has($creatives, 'creativeTypes')) {
                BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
                return;
            }
            $creativeTypes = collect(Arr::get($creatives, 'creativeTypes', []));
            if ($creativeTypes->count() == 1) {
                $creativeType = $creativeTypes->first();
                $material = $this->ad->materials()->first();
                $material->creative_id = $creativeType['id'];
                $material->status = $creativeType['state'];
                $material->save();
                BLogger::scope(['uchcv2', 'job'])->debug(__FUNCTION__, array_merge($creativeType, [
                    'ad_id' => $this->ad->ad_id,
                    'material_id' => $material->id,
                ]));
                $this->uchcv2->creative()->updateOrCreate(
                    [
                        'creativeId' => $creativeType['id']
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
            BLogger::scope(['uchcv2', 'job'])->notice('推送广告创意无', $this->descMe());
        }
    }


    /**
     * 拉取推广创意
     *
     * @return void
     */
    public function pullCreative()
    {
        $creativeIds = [];

        if ($this->ad && $this->uchcv2 && $this->uchcv2->creative) {
            $creativeIds = [$this->uchcv2->creative->creativeId];
        } elseif (Arr::has($this->params, 'creativeIds')) {
            $creativeIds = Arr::get($this->params, 'creativeIds', null);
            if (!is_array(!$creativeIds)) {
                $creativeIds = [$creativeIds];
            }
        }

        $res = null;
        if (sizeof($creativeIds) > 0) {
            $res = $this->sdk->getCreativeByCreativeId($creativeIds);
        }

        if (!Arr::has($res, 'creativeTypes')) {
            BLogger::scope(['uchcv2', 'job'])->error(__FUNCTION__, $this->descMe());
            return;
        }

        collect($res['creativeTypes'])->each(function ($item) {
            $creative = Uchcv2Creative::query()
                ->where(['creativeId' => Arr::get($item, 'id')])
                ->first();
            if (!$creative) {
                return;
            }
            $creative->fill($item);
            $creative->save();
        });
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
            Uchcv2::where('promote_id', $this->promote->promote_id)
                ->select('ad_id', 'promote_id')
                ->get()
                ->each(function ($item) use (&$ad_ids) {
                    $ad_ids[] = $item->ad_id;
                });
            $creatives = Uchcv2Creative::query()
                ->with(['ad' => function ($query) {
                    $query->without(['materials'])
                        ->select('ad_id', 'distribute_sync', 'distribute_msg');
                }])
                ->whereIn('ad_id', $ad_ids)
                ->select('id', 'ad_id', 'creativeId', 'state', 'refuseReason', 'invalidReasonList')
                ->get();
            $creatives->each(function ($item) use (&$creativeIds) {
                $creativeIds[] = $item->creativeId;
            });
        } else {
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        $res = null;
        if (sizeof($creativeIds) > 0) {
            $res = $this->sdk->getCreativeByCreativeId($creativeIds);
        }
        if (Arr::has($res, 'creativeTypes')) {
            collect($res['creativeTypes'])->each(function ($item) use (&$creatives) {
                $creative = $creatives->where('creativeId', $item['id'])->first();
                if (!$creative) {
                    return;
                }
                $invalidReasonArr = Arr::get($item, 'invalidReasonList', []);
                if (Arr::get($item, 'refuseReason', '')) {
                    array_push($invalidReasonArr, Arr::get($item, 'refuseReason', ''));
                }
                foreach (['state', 'refuseReason', 'invalidReasonList'] as $key => $value) {
                    $creative->$value = $item[$value];
                }
                $creative->save();

                if ($creative->ad) {
                    if (sizeof($invalidReasonArr) > 0) {
                        $creative->ad->distribute_sync = 3;
                        $creative->ad->distribute_msg = implode('; ', $invalidReasonArr);
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
            BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
        }
    }

    /**
     * 收集本地分发创意数据
     *
     * @return Array
     */
    public function assemCreativeData()
    {
        $creativeTypes = [];
        $files = collect($this->uploadFiles());

        $this->ad
            ->materials()
            // ->withTrashed()
            ->each(function ($material, $materialKey) use (&$creativeTypes, $files) {
                // print_r($material->uchcv2Creative); die;
                $style = $material->style;
                $styleInfo = json_decode($style->style_info);
                $creativeItem = [];
                // 创意素材修改
                if ($material->creative_id) {
                    $creativeItem['id'] = intval($material->creative_id);
                }
                $creativeItem = array_merge($creativeItem, [
                    'adGroupId' => $this->adGroup->adGroupId, // number
                    'campaignId' => $this->campaign->campaignId, // string
                    'style' => intval(trim($style->enumerated_value)), // string, 样式id通过getCreativeTemplate获取
                    'showMode' => 1,
                    'paused' => $material->creative ? $material->creative->paused : false, // 无效
                    // 'wildcards' => null, // 选填 词包id
                    // 'state' => '', // 无效
                    // 'videoId' => '', // 视频创意必填 视频id
                    'index' => $materialKey, // 选填 默认0, 用于标识返回失败对象列表
                ]);
                if ($this->ad->track_url) {
                    $creativeItem['clickMonitorUrl'] = $this->ad->track_url; // string 选填
                }

                $creativeContent = [
                    'source' => $this->uchcv2->creativeSource,
                    'target_url' => $this->ad->shell_url
                ];
                $materialContent = collect(json_decode($material->content));
                $materialContent->each(function ($item, $key) use (&$creativeContent, &$creativeItem, $styleInfo, $files) {
                    if ($key == 'text') {
                        collect($item)->each(function ($item, $key) use (&$creativeContent, &$creativeItem) {
                            $creativeContent[$key] = $item->value;
                            if (isset($item->wildcardIds)) {
                                $creativeItem['wildcards'] = $item->wildcardIds;
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
            $file = Uchcv2File::where('promote_id', $this->promote->promote_id)
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
                //         $file = Uchcv2File::create([
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
                if (Arr::has($res, 'mcVideos')) {
                    $mcVideos = Arr::get($res, 'mcVideos', []);
                    foreach ($mcVideos as $key => $mcVideo) {
                        $file = Uchcv2File::updateOrCreate(
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
                if (Arr::has($res, 'taskId')) {
                    self::dispatch(
                        'PULL_MY_REPORT',
                        array_merge($this->params, [
                            'phase' => 'state',
                            'reportType' => 'creative',
                            'taskId' => $res['taskId'],
                            'body' => $res,
                        ])
                    )
                        ->delay(5);
                } else {
                    BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, $this->descMe());
                }
                break;
            case 'state':
                $taskInfo = $this->sdk->getFile($this->params['taskId']);
                $reportReady = Arr::get($taskInfo, 'reportFileType.ready');
                if ($reportReady) {
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
                break;
            case 'download':
                $body = $this->params['body'];
                $taskInfo = $this->params['taskInfo'];

                $file = $this->sdk->downloadFile($this->params['taskId']);
                if ($file) {
                    $storage = SdkUchcv2::getStorage();
                    $report = implode(
                        DIRECTORY_SEPARATOR,
                        [
                            SdkUchcv2::$StorageBaseDir,
                            Arr::get($this->params, 'reportType'),
                            implode('_', [
                                Arr::get($this->params, 'promote_id'),
                                $body['taskId'],
                            ]) . '.csv'
                        ]
                    );
                    SdkUchcv2::transCsv($file);
                    $storage->move($file, $report);
                    self::dispatch(
                        'PULL_MY_REPORT',
                        array_merge($this->params, [
                            'phase' => 'fetch_creative',
                            'report' => $report
                        ])
                    )
                        ->delay(5);
                }
                break;
            case 'fetch_creative':
                $storage = SdkUchcv2::getStorage();
                $report = $this->params['report']; // 报表文件

                // 读取报表
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setReadDataOnly(true);
                $reader->setInputEncoding('GB2312');
                $reader->setEnclosure('');
                $spreadsheet = $reader->load($storage->path($report));

                // 解析数据
                $fields = [
                    "dw_date" => "日期",
                    "userId" => "账户ID",
                    "userName" => "账户",
                    "campaignId" => "推广组ID",
                    "campaignName" => "推广组",
                    "adGroupId" => "推广计划ID",
                    "adGroupName" => "推广计划",
                    "creativeId" => "创意ID",
                    "creativeName" => "创意标题",
                    "creativeStyle" => "创意样式",
                    "objective" => "推广对象",
                    "chargeType" => "计费方式",
                    "show" => "展现数",
                    "click" => "点击数",
                    "stat_cost" => "消费",
                    "click_rate" => "点击率",
                    "avg_click_price" => "点击均价",
                    "cpm_price" => "千次展现均价",
                    "convert_type" => "转化类型",
                    "convert_count" => "转化数",
                    "convert_rate" => "转化率",
                    "convert_cost" => "转化成本",
                    "android_download_start_count" => "安卓下载开始数",
                    "android_download_start_cost" => "安卓下载开始成本",
                    "android_download_finish_count" => "安卓下载完成数",
                    "android_download_finish_rate" => "安卓下载完成率",
                    "android_download_finish_cost" => "安卓下载完成成本",
                ];
                $fieldKeys = array_keys($fields);
                $fieldVals = array_values($fields);

                $list = $spreadsheet->getSheet(0)->toArray();
                $listHead = array_shift($list);

                // 保存数据
                if (count(array_intersect($fieldVals, $listHead)) == count($fieldVals)) {
                    // 根据创意批量查询广告ID
                    $creativeIdPos = array_search('creativeId', $fieldKeys);
                    $creativeIds = collect($list)->map(function ($item) use ($creativeIdPos) {
                        return $item[$creativeIdPos];
                    })->unique();

                    $creativeAdKV = [];
                    $creativeMaterialKV = [];
                    Uchcv2Creative::query()
                        ->whereIn('creativeId', $creativeIds)
                        ->select('ad_id', 'creativeId', 'material_id')
                        ->get()
                        ->each(function ($item) use (&$creativeAdKV, &$creativeMaterialKV) {
                            $creativeAdKV[$item->creativeId] = $item->ad_id;
                            $creativeMaterialKV[$item->creativeId] = $item->material_id;
                        });

                    // 插入创意报表
                    collect($list)->each(function ($item) use ($fieldKeys, $creativeAdKV, $creativeMaterialKV, $creativeIdPos) {
                        self::transArrayVal($item);
                        if (isset($creativeAdKV[$item[$creativeIdPos]]) && isset($creativeMaterialKV[$item[$creativeIdPos]])) {
                            $report = [];
                            for ($i = 0; $i < sizeof($item); $i++) {
                                $fieldKey = $fieldKeys[$i];
                                if (in_array($fieldKey, ['userName', 'campaignName', 'adGroupName'])) {
                                    continue;
                                }
                                if ($fieldKey == 'dw_date') {
                                    $report[$fieldKey] = Carbon::createFromFormat('Ymd', $item[$i])->format('Y-m-d');
                                } else {
                                    $report[$fieldKey] = $item[$i];
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
                                Uchcv2DwCreative::updateOrCreate([
                                    'dw_date' => $report['dw_date'],
                                    'userId' => $report['userId'],
                                    'campaignId' => $report['campaignId'],
                                    'adGroupId' => $report['adGroupId'],
                                    'creativeId' => $report['creativeId'],
                                ], array_merge($report, [
                                    'ad_id' => $creativeAdKV[$report['creativeId']],
                                    'content' => json_encode($$report)
                                ]));
                            }
                        }
                    });
                } else {
                    BLogger::scope(['uchcv2', 'job'])->warning(__FUNCTION__, [$this->descMe(), $fieldVals, $listHead, $list]);
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
            BLogger::scope(['uchcv2', 'job'])->error(__FUNCTION__, [$zone, $this->descMe(), $sync, $e]);
        }
        BLogger::scope(['uchcv2', 'job'])->info(__FUNCTION__, [$zone, $this->descMe(), $sync, $e, $msg]);
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
                    Uchcv2Campaign::whereIn('campaignId', $this->params['campaignIds'])
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
            // $this->pullAdGroup(); // 失败回滚计划
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
