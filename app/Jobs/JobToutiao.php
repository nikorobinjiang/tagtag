<?php

namespace App\Jobs;

use App\Libraries\BLogger;
use App\Libraries\SdkToutiao;

use App\Models\Advertisement;
use App\Models\AdMaterial;
use App\Models\MaterialAnnexs;
use App\Models\Promote;

use App\Models\Toutiao;
use App\Models\ToutiaoAccount;
use App\Models\ToutiaoFile;
use App\Models\ToutiaoGroup;

use App\Models\AdDwMaterial;
use App\Models\ToutiaoDwCreative;

use App\Exceptions\JobException as Exception;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class JobToutiao extends Job
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'toutiao';

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
     * @var \App\Libraries\SdkToutiao
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
     * @var \App\Models\ToutiaoGroup
     */
    protected $group;

    /**
     * 当前操作的广告.
     *
     * @var \App\Models\Advertisement
     */
    protected $ad;

    /**
     * 第三方账号.
     *
     * @var \App\Models\ToutiaoAccount
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
     * 当前操作的头条对象.
     *
     * @var \App\Models\Toutiao
     */
    protected $toutiao;

    /**
     * 过程数据存放点.
     *
     * @var Object
     */
    protected $data;

    /**
     * 创建一个新的头条相关的 job 实列，需要严格按照 uses 中的示例使用并传参。
     *
     * @param  String $action 可选值: PUSH_GROUP | PULL_MY_GROUPS | PULL_MY_PLAN_STATUS| PUSH_AD | PUSH_PLAN_OPT_STATUS
     * @param  Object $params
     *
     * @return void
     *
     * @uses __construct('PUSH_GROUP', ['group_id' => 0]) 推送广告组
     * @uses __construct('PULL_MY_GROUPS', ['promote_id' => 0]) 拉取对应媒体账号的广告组
     * @uses __construct('PULL_MY_PLAN_STATUS', ['promote_id' => 0]) 拉取广告主的所有计划状态
     * @uses __construct('PULL_MY_PLAN_STATUS', ['account_id' => 0]) 拉取广告主的所有计划状态
     * @uses __construct('PUSH_AD', ['ad_id' => 0]) 推送广告计划及其创意
     * @uses __construct('PUSH_PLAN_OPT_STATUS', ['ad_id' => 0]) 拉取对应广告的状态
     *
     */
    public function __construct($action, $params, $data = null)
    {
        BLogger::scope(['toutiao', 'job'])->info(__FUNCTION__, [$action, $params]);
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
            case 'PULL_MY_FUND':
                $this->pullMyFund();
                break;
            case 'PUSH_GROUP':
                $this->pushCampaign();
                break;
            case 'PULL_MY_GROUPS':
                $this->pullMyCampaigns();
                break;
            case 'PULL_MY_PLAN_STATUS': // 拉取某个广告主下的所有计划状态
                $this->pullMyPlanStatus();
                break;
            case 'PUSH_PLAN_START':
                $this->pushPlanStart();
                break;
            case 'PUSH_PLAN_END':
                $this->pushPlanEnd();
                break;
            case 'PUSH_PLAN':
                $this->pushPlan();
                break;
            case 'PULL_PLAN':
                $this->pullPlan();
                break;
            case 'PUSH_PLAN_SCHEDULE':
                $this->pushPlanSchedule();
                break;
            case 'PULL_PLAN_STATUS':
                $this->pullPlanStatus();
                break;
            case 'PUSH_CREATIVE':
                $this->pushCreative();
                break;
            case 'PULL_CREATIVE_ID':
                $this->pullCreativeID();
                break;
            case 'PUSH_CREATIVE_OPT_STATUS':
                $this->pushCreativeOptStatus();
                break;
            case 'PULL_CREATIVE_OPT_STATUS':
                $this->pullCreativeOptStatus();
                break;
            case 'PUSH_AD':
                $this->params['zone'] = 'PUSH_AD';
                self::withChain([
                    new self('PUSH_PLAN_START', $this->params),
                    new self('PUSH_PLAN', $this->params),
                    new self('PULL_PLAN_STATUS', $this->params),
                    new self('PUSH_CREATIVE', $this->params),
                    new self('PULL_CREATIVE_ID', $this->params),
                    new self('PULL_CREATIVE_OPT_STATUS', $this->params),
                    new self('PUSH_PLAN_END', $this->params),
                ])
                    ->dispatch('EMPTY', $this->params);
                break;
            case 'PUSH_PLAN_OPT_STATUS': // 推送广告计划状态
                $this->pushPlanOptStatus();
                break;
            case 'PUSH_PLAN_BID':
                $this->pushPlanBid();
                break;
            case 'PUSH_PLAN_BUDGET':
                $this->pushPlanBudget();
                break;
            case 'PULL_MY_REPORT':
                $this->pullMyReport();
                break;
            case 'EMPTY':
                break;
            default:
                BLogger::scope(['toutiao', 'job'])->warning('该任务不再执行范围内未查询到', $this->descMe());
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
        $this->sdk = new SdkToutiao();

        if (isset($this->params['ad_id'])) {
            $this->ad = Advertisement::find($this->params['ad_id']);
            if ($this->ad) {
                $this->materials = $this->ad->materials;
                $this->toutiao = $this->ad->toutiao;
                $this->promote = $this->ad->promote;
            }
        }

        if (isset($this->params['group_id'])) {
            $this->group = ToutiaoGroup::query()
                ->find($this->params['group_id']);
            if (!$this->group) {
                throw new Exception('当前广告组不存在');
            }
            $this->promote = $this->group->promote;
        }

        if (isset($this->params['account_id'])) {
            $this->account = ToutiaoAccount::where('promote_id', $this->params['account_id'])->first();
            $this->promote = $this->account->promote;
        }

        if (isset($this->params['promote_id'])) {
            $this->promote = Promote::find($this->params['promote_id']);
        }

        if ($this->promote) {
            $this->account = ToutiaoAccount::where('promote_id', $this->promote->promote_id)->first();
            $this->advertiser_id = floatval($this->account->advertiser_id);
            $this->sdk->loadByPromote($this->promote->promote_id);
        }
    }

    /**
     * 拉取广告主余额
     *
     * @return void
     */
    public function pullMyFund()
    {
        $res = $this->sdk->advertiserFundGet([
            'advertiser_id' => $this->advertiser_id,
        ]);

        if ($res && array_key_exists('data', $res)) {
            $fund = $res['data'];
            $this->account->email = $fund['email'];
            $this->account->balance = $fund['balance'];
            $this->account->valid_balance = $fund['valid_balance'];
            $this->account->cash = $fund['cash'];
            $this->account->valid_cash = $fund['valid_cash'];
            $this->account->grant = $fund['grant'];
            $this->account->valid_grant = $fund['balance'];
            $this->account->save();
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
        }
    }

    /**
     * 推送广告组
     *
     * @return void
     */
    public function pushCampaign()
    {
        $form = [
            'advertiser_id' => $this->advertiser_id,
            'campaign_name' => $this->group->campaign_name,
            'budget_mode' => $this->group->budget_mode,
            'landing_type' => $this->group->landing_type
        ];

        if ($this->group->budget_mode == 'BUDGET_MODE_DAY') {
            $form['budget'] = $this->group->budget;
        }

        $res = null;
        if ($this->group->campaign_id) {
            $this->pullCampaignStatus();
            $form['campaign_id'] = $this->group->campaign_id;
            $form['modify_time'] = $this->group->modify_time;
            $res = $this->sdk->campaignUpdate($form);
        } else {
            $res = $this->sdk->campaignCreate($form);
        }
        if ($res && array_key_exists('data', $res)) {
            // 接口返回正确 修改数据库同步状态
            $this->group->distribute_sync = 4;
            $this->group->distribute_msg = '同步时间:' . date('Y-m-d H:i:s', time());
            $this->group->campaign_id = $res['data']['campaign_id'];
            $this->group->save();
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /*
     * 拉取广告组状态
     * */
    public function pullCampaignStatus()
    {
        $page = 1;
        $ids = [$this->group->campaign_id];
        $query = [
            'advertiser_id' => $this->advertiser_id,
            'page_size' => 10,
            'page' => $page
        ];
        if (count($ids) > 0) {
            $query['filtering'] = ['ids' => $ids];
        }
        $res = $this->sdk->campaignGet($query);
        if ($res && array_key_exists('data', $res)) {
            // page
            $page_info = $res['data']['page_info'];
            if ($page_info['total_page'] > $page_info['page']) {
                $query = [
                    'advertiser_id' => $this->advertiser_id,
                    'page_size' => $page_info['total_number'],
                    'page' => $page
                ];
                if (count($ids) > 0) {
                    $query['filtering'] = ['ids' => $ids];
                }
                $res = $this->sdk->campaignGet($query);
            }
            $list = collect($res['data']['list']);
            $list->each(function ($item) {
                ToutiaoGroup::query()
                    ->where('campaign_id', $item['id'])
                    ->update([
                        'modify_time' => $item['modify_time'],
                        'status' => $item['status'],
                    ]);
            });
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /**
     * 拉取广告组(新)
     *
     * @return void
     */
    public function pullMyCampaigns($ids = [], $page = 1)
    {
        $query = [
            'advertiser_id' => $this->advertiser_id,
            'page_size' => 10,
            'page' => $page
        ];
        if (count($ids) > 0) {
            $query['filtering'] = ['ids' => $ids];
        }
        $res = $this->sdk->campaignGet($query);
        if ($res && array_key_exists('data', $res)) {
            // page
            $page_info = $res['data']['page_info'];
            if ($page_info['total_page'] > $page_info['page']) {
                $query = [
                    'advertiser_id' => $this->advertiser_id,
                    'page_size' => $page_info['total_number'],
                    'page' => $page
                ];
                if (count($ids) > 0) {
                    $query['filtering'] = ['ids' => $ids];
                }
                $res = $this->sdk->campaignGet($query);
            }
            $list = collect($res['data']['list']);
            $list->each(function ($item) {
                if (in_array($item['landing_type'], ['APP'])) {
                    $insdata = [
                        "promote_id" => $this->promote->promote_id,
                        "media_id" => $this->promote->media_id,
                        "advertiser_id" => $this->advertiser_id,
                        "budget_mode" => $item['budget_mode'],
                        "landing_type" => $item['landing_type'],
                        "campaign_name" => $item['name'],
                        "budget" => $item['budget'],
                        "status" => $item['status'],
                        "campaign_id" => $item['id']
                    ];
                    if (isset($item['modify_time'])) {
                        $insdata['modify_time'] = $item['modify_time'];
                    }
                    ToutiaoGroup::updateOrCreate([
                        'promote_id' => $this->promote->promote_id,
                        'campaign_id' => $item['id']
                    ], $insdata);
                }
            });
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, [$this->descMe(), $res]);
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /**
     * 创建转化ID, 为 OCPC、OCPM 广告服务
     * 需要初始化 sdk, 加载 ad & toutiao
     *
     * @return void
     */
    public function createConvert()
    {
        $selectQuery = [
            'advertiser_id' => $this->advertiser_id
        ];

        $form = [
            'advertiser_id' => $this->advertiser_id,
            'name' => $this->ad->ad_name,
            'download_url' => $this->ad->apk_addr,
            'app_type' => $this->toutiao->app_type, // 允许值: "APP_ANDROID", "APP_IOS"
            'action_track_url' => '',
            'package_name' => $this->ad->game->bundleid,
            'convert_type' => 'AD_CONVERT_TYPE_ACTIVE',
        ];
        if ($form['app_type'] == 'APP_ANDROID') {
            $selectQuery['package_name'] = $form['package_name'];
            // $form['action_track_url'] = 'http://www.gm88.com/track/?adid=__AID__&cid=__CID__&imei=__IMEI__&mac=__MAC__&androidid=__ANDROIDID1__&os=__OS__&timestamp=__TS__&callback_url=__CALLBACK_URL__';
        } elseif ($form['app_type'] == 'APP_IOS') {
            $selectQuery['itunes_url'] = $form['package_name'];
            // $form['action_track_url'] = 'http://www.gm88.com/track/?adid=__AID__&cid=__CID__&imei=__idfa__&mac=__MAC__&os=__OS__&timestamp=__TS__&callback_url=__CALLBACK_URL__';
            // $form['action_track_url'] = $this->ad->track_url;
        }

        // 查询是否已创建
        $convertList = $this->sdk->toolsAdConvertSelect($selectQuery);

        $res = $this->sdk->toolsAdConvertCreate($form);
        if ($res && array_key_exists('data', $res)) {
            $this->toutiao->convert_id = $res['data']['id'];
            $this->toutiao->save();
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
        return $res;
    }

    /**
     * 拉取广告主的所有计划状态
     *
     * @return void
     */
    public function pullMyPlanStatus()
    {
        $plan_ids = [];
        $toutiaos = $this->account->toutiaos()
            ->with(['ad' => function ($query) {
                $query->without(['materials'])
                    ->select('ad_id', 'status');
            }])
            ->where('plan_id', '<>', 0)
            ->where('plan_id', '<>', "")
            ->whereNotIn('status', ['AD_STATUS_DISABLE', 'AD_STATUS_CAMPAIGN_DISABLE'])
            ->get();
        $toutiaos->each(function ($item) use (&$plan_ids) {
            $plan_ids[] = $item->plan_id;
        });

        if (sizeof($plan_ids) > 0) {
            $plansPage = 0;
            do {
                $res = $this->sdk->adGet([
                    '__caller__' => __FUNCTION__,
                    'page' => ++$plansPage,
                    'advertiser_id' => $this->advertiser_id,
                    'filtering' => [
                        'ids' => $plan_ids
                    ]
                ]);

                if ($res && array_key_exists('data', $res)) {
                    collect($res['data']['list'])->map(function ($item) use ($toutiaos) {
                        $toutiao = $toutiaos->firstWhere('plan_id', $item['id']);
                        if ($toutiao) {
                            $toutiao->status = $item['status'];
                            $toutiao->opt_status = $item['opt_status'];
                            $toutiao->audit_reject_reason = $item['audit_reject_reason'];
                            $toutiao->modify_time = $item['modify_time'];
                            $toutiao->ad_modify_time = $item['ad_modify_time'];
                            $toutiao->ad_create_time = $item['ad_create_time'];
                            $toutiao->save();
                            if ($toutiao->ad) {
                                $toutiao->ad->status = $item['opt_status'] === 'AD_STATUS_ENABLE' ? 1 : 0;
                                $toutiao->ad->save();
                            }
                        }
                    });
                } else {
                    BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
                    throw new Exception($res['message'] ?: __FUNCTION__);
                }
            } while (isset($res['data']) && isset($res['data']['list']) && sizeof($res['data']['list']) > 0);
        }
    }

    /**
     * 推送头条计划 开始
     *
     * @return void
     */
    public function pushPlanStart()
    {
        // 同步状态：同步中
        $this->ad->distribute_sync = 2;
        $this->ad->save();
    }

    /**
     * 推送头条计划 结束
     *
     * @return void
     */
    public function pushPlanEnd()
    {
        // 同步状态：可同步
        if ($this->ad->distribute_sync == 2) {
            $this->ad->distribute_sync = 1;
            $this->ad->distribute_msg = '';
            $this->ad->save();
        }
    }

    /**
     * 推送头条计划
     *
     * @return void
     */
    public function pushPlan()
    {
        $res = null;
        $pushAction = '';
        if ($this->toutiao->plan_id) {
            $pushAction = 'update';
            $this->pullPlanStatus();
            $form = $this->collectPlanData();
            $form['ad_id'] = $this->toutiao->plan_id;
            $form['modify_time'] = $this->toutiao->modify_time;
            $res = $this->sdk->adUpdate($form);
        } else {
            $pushAction = 'create';
            // 判断 OCPC、OCPM 出价方式，并创建转化ID (已弃用)
            // if ($this->toutiao->pricing == 'PRICING_OCPC' && !$this->toutiao->convert_id) {
            //     $this->createConvert();
            // }
            $form = $this->collectPlanData();
            $res = $this->sdk->adCreate($form);
        }

        if (in_array($res['code'], [40001])) {
            throw new Exception($res['message'] ?: '头条接口请求异常');
        } else if ($res && Arr::has($res, 'data')) {
            $this->toutiao->plan_id = $res['data']['ad_id'];
            $this->toutiao->title = $form['name'];
            $this->toutiao->advertiser_id = $form['advertiser_id'];
            $this->toutiao->campaign_id = $form['campaign_id'];
            $this->toutiao->save();
            // 推送广告计划状态
            $this->pushPlanOptStatus();
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            if ($res['message'] !== '没有任何修改') {
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
        }
    }

    /**
     * 拉取头条计划
     *
     * @return void
     */
    public function pullPlan()
    {
        $plan_ids = [];
        if ($this->toutiao && $this->toutiao->plan_id) {
            $plan_ids[] = $this->toutiao->plan_id;
        }

        if (sizeof($plan_ids) == 0) {
            return;
        }

        $res = $this->sdk->adGet([
            '__caller__' => __FUNCTION__,
            'advertiser_id' => $this->advertiser_id,
            'filtering' => ['ids' => $plan_ids]
        ]);

        if ($res && Arr::has($res, 'data.list')) {
            $item = collect($res['data']['list'])->firstWhere('id', $this->toutiao->plan_id);
            if ($item) {
                $this->toutiao->budget_mode = $item['budget_mode'];
                $this->toutiao->budget = $item['budget'];
                $this->toutiao->start_time = $item['start_time'];
                $this->toutiao->end_time = $item['end_time'];
                $this->toutiao->bid = $item['bid'];
                $this->toutiao->pricing = $item['pricing'];
                $this->toutiao->schedule_type = $item['schedule_type'];
                $this->toutiao->schedule_time = $item['schedule_time'];
                $this->toutiao->flow_control_mode = $item['flow_control_mode'];
                // $this->toutiao->open_url = $item['open_url'];
                // $this->toutiao->external_url = $item['external_url'];
                // $this->toutiao->download_url = $item['download_url'];
                $this->toutiao->app_type = $item['app_type'];
                // $this->toutiao->package = $item['package'];
                // $this->toutiao->cpa_bid = $item['cpa_bid'];
                // $this->toutiao->convert_id = $item['convert_id'];
                // $this->toutiao->hide_if_converted = $item['hide_if_converted'];

                // audience
                $audience = $item['audience'];
                $this->toutiao->retargeting_tags_include = $audience['retargeting_tags_include'];
                $this->toutiao->retargeting_tags_exclude = $audience['retargeting_tags_exclude'];
                $this->toutiao->gender = isset($audience['gender']) ? $audience['gender'] : 'NONE';
                $this->toutiao->age = isset($audience['age']) ? $audience['age'] : [
                    "AGE_BELOW_18", "AGE_BETWEEN_18_23", "AGE_BETWEEN_24_30", "AGE_BETWEEN_31_40", "AGE_BETWEEN_41_49", "AGE_ABOVE_50"
                ];
                $this->toutiao->android_osv = isset($audience['android_osv']) ? $audience['android_osv'] : '0.0';
                // $this->toutiao->ios_osv = $audience['ios_osv'];
                $this->toutiao->ac = isset($audience['ac']) ? $audience['ac'] : ["WIFI", "2G", "3G", "4G"];
                // $this->toutiao->device_brand = $audience['device_brand'];
                // $this->toutiao->article_category = $audience['article_category'];
                // $this->toutiao->activate_type = $audience['activate_type'];
                $this->toutiao->platform = isset($audience['platform']) ? $audience['platform'] : [];
                // $this->toutiao->carrier = $audience['carrier'];
                $this->toutiao->ad_tag = isset($audience['ad_tag']) ? $audience['ad_tag'] : [];
                // $this->toutiao->interest_tags = $audience['interest_tags'];
                $this->toutiao->city = isset($audience['city']) ? $audience['city'] : [];
                $this->toutiao->location_type = isset($audience['location_type']) ? $audience['location_type'] : 'CURRENT';
                // $this->toutiao->app_behavior_target = $audience['app_behavior_target'];
                // $this->toutiao->app_category = $audience['app_category'];
                // $this->toutiao->app_ids = $audience['app_ids'];
                // $this->toutiao->product_platform_id = $audience['product_platform_id'];
                // $this->toutiao->external_url_params = $audience['external_url_params'];
                // $this->toutiao->open_url_params = $audience['open_url_params'];
                // $this->toutiao->dpa_local_audience = $audience['dpa_local_audience'];
                // $this->toutiao->include_custom_actions = $audience['include_custom_actions'];
                // $this->toutiao->exclude_custom_actions = $audience['exclude_custom_actions'];

                $this->toutiao->status = $item['status'];
                $this->toutiao->opt_status = $item['opt_status'];
                $this->toutiao->audit_reject_reason = $item['audit_reject_reason'];
                $this->toutiao->modify_time = $item['modify_time'];
                $this->toutiao->ad_modify_time = $item['ad_modify_time'];
                $this->toutiao->ad_create_time = $item['ad_create_time'];
                $this->toutiao->save();
            }
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /**
     * 推送头条计划时间
     *
     * @return void
     */
    public function pushPlanSchedule()
    {
        $res = null;
        if ($this->toutiao->plan_id) {
            $this->pullPlanStatus();
            $form = [];
            collect($this->collectPlanData())->map(function ($val, $key) use (&$form) {
                if (in_array($key, ['end_time', 'schedule_type', 'schedule_time'])) {
                    $form[$key] = $val;
                }
            });
            $form['ad_id'] = $this->toutiao->plan_id;
            $form['modify_time'] = $this->toutiao->modify_time;
            $res = $this->sdk->adUpdate($form);
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, ['当前广告还未同步计划', $this->descMe()]);
            throw new Exception('当前广告还未同步计划');
        }

        if ($res && array_key_exists('data', $res)) {
            $this->toutiao->end_time = $res['data']['end_time'];
            $this->toutiao->schedule_type = $res['data']['schedule_type'];
            $this->toutiao->schedule_time = $res['data']['schedule_time'];
            $this->toutiao->modify_time = $res['data']['modify_time'];
            $this->toutiao->save();
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            if ($res['message'] !== '没有任何修改') {
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
        }
    }

    /**
     * 拉取广告计划状态
     *
     * @return void
     */
    public function pullPlanStatus()
    {
        $res = $this->sdk->adGet([
            '__caller__' => __FUNCTION__,
            'advertiser_id' => $this->advertiser_id,
            'filtering' => ['ids' => [$this->toutiao->plan_id]]
        ]);

        if ($res && array_key_exists('data', $res)) {
            $item = collect($res['data']['list'])->firstWhere('id', $this->toutiao->plan_id);
            if ($item) {
                $this->toutiao->status = $item['status'];
                $this->toutiao->opt_status = $item['opt_status'];
                $this->toutiao->audit_reject_reason = $item['audit_reject_reason'];
                $this->toutiao->modify_time = $item['modify_time'];
                $this->toutiao->ad_modify_time = $item['ad_modify_time'];
                $this->toutiao->ad_create_time = $item['ad_create_time'];
                $this->toutiao->save();
                $adUpdate = [];
                if (isset($item['opt_status'])) {
                    $adUpdate['status'] = $item['opt_status'] == 'AD_STATUS_ENABLE' ? 1 : 0;
                }
                if (sizeof($adUpdate) > 0) {
                    Advertisement::select('ad_id')
                        ->where('ad_id', $this->toutiao->ad_id)
                        ->where('distribute', 'Toutiao')
                        ->update($adUpdate);
                }
            }
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /**
     * 推送广告计划状态
     *
     * @return void
     */
    public function pushPlanOptStatus()
    {
        $res = [];
        $plan_ids = [];
        $opt_status = '';
        if ($this->ad) {
            $plan_ids = [$this->toutiao->plan_id];
            $opt_status = $this->ad->status == 1 ? 'enable' : 'disable';
        } elseif (
            isset($this->params['status']) &&
            is_numeric($this->params['status']) &&
            isset($this->params['ad_ids']) &&
            is_array($this->params['ad_ids']) &&
            isset($this->params['promote_id']) &&
            $this->params['promote_id'] &&
            $this->sdk->loadByPromote($this->params['promote_id'])
        ) {
            $plan_ids = $this->params['ad_ids'];
            $opt_status = $this->params['status'] == 1 ? 'enable' : 'disable';
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }
        $form = [
            'advertiser_id' => $this->advertiser_id,
            'ad_ids' => $plan_ids,
            'opt_status' => $opt_status,
        ];
        $res = $this->sdk->adUpdateStatus($form);

        if ($res && array_key_exists('data', $res)) {
            Toutiao::whereIn('plan_id', $res['data']['ad_ids'])
                ->update([
                    'opt_status' => $opt_status == 'enable'
                        ? 'AD_STATUS_ENABLE'
                        : 'AD_STATUS_DISABLE'
                ]);
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /**
     * 更新计划预算
     *
     * @return void
     */
    public function pushPlanBudget()
    {
        $res = [];
        $ad_id = null;
        $budget = 0;
        $data = [];
        $pushType = ''; // batch | single
        if (isset($this->params['data']) && is_array($this->params['data'])) {
            $pushType = 'batch';
            $data = $this->params['data'];
            $form = [
                'advertiser_id' => $this->advertiser_id,
                'data' => $data
            ];
            $res = $this->sdk->adUpdateBudget($form);
        } elseif ($this->ad) {
            $pushType = 'single';
            $budget = isset($this->params['budget']) ? $this->params['budget'] : $this->toutiao->budget;
            $form = [
                'advertiser_id' => $this->advertiser_id,
                'ad_id' => $this->toutiao->plan_id,
                'budget' => $budget
            ];
            $res = $this->sdk->adUpdateBudget($form);
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        if ($res && array_key_exists('data', $res)) {
            switch ($pushType) {
                case 'batch':
                    foreach ($data as $key => $val) {
                        if (in_array($val['ad_id'], $res['data']['ad_ids'])) {
                            Toutiao::query()
                                ->where('plan_id', $val['ad_id'])
                                ->update(['budget' => $val['budget']]);
                        }
                    }
                    break;
                case 'single':
                    Toutiao::query()
                        ->where('plan_id', $res['data']['ad_id'])
                        ->update(['budget' => $budget]);
                    break;
            }
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /**
     * 推送广告计划出价
     *
     * @return void
     */
    public function pushPlanBid()
    {
        $res = [];
        $ad_ids = [];
        $pricing = '';
        $is_stage2bid = 0;
        $bid = 0;
        if ($this->ad) {
            $pricing = str_replace('PRICING_', '', $this->toutiao->pricing);
            $is_stage2bid = in_array($pricing, ['OCPC', 'OCPM']) ? 1 : 0;
            if ($is_stage2bid == 1) {
                $bid = $this->toutiao->cpa_bid;
            } else {
                $bid = $this->toutiao->bid;
            }
            $ad_ids = [$this->toutiao->plan_id];
            $form = [
                'advertiser_id' => $this->advertiser_id,
                'ad_ids' => $ad_ids,
                'is_stage2bid' => $is_stage2bid,
                'bid' => $bid
            ];
            $res = $this->sdk->adUpdateBid($form);
        } elseif (
            isset($this->params['plan_ids']) &&
            is_array($this->params['plan_ids']) &&
            isset($this->params['bid']) &&
            is_numeric($this->params['bid']) &&
            isset($this->params['pricing']) &&
            is_string($this->params['pricing']) &&
            isset($this->params['promote_id']) &&
            $this->params['promote_id'] &&
            $this->sdk->loadByPromote($this->params['promote_id'])
        ) {
            $pricing = $this->params['pricing'];
            $is_stage2bid = in_array($pricing, ['OCPC', 'OCPM']) ? 1 : 0;
            $bid = $this->params['bid'];
            $plan_ids = $this->params['plan_ids'];
            $form = [
                'advertiser_id' => $this->advertiser_id,
                'ad_ids' => $plan_ids,
                'is_stage2bid' => $is_stage2bid,
                'bid' => $bid
            ];
            $res = $this->sdk->adUpdateBid($form);
        } else {
            BLogger::scope(['uchc', 'job'])->warning(__FUNCTION__, $this->descMe());
            return;
        }

        if ($res && array_key_exists('data', $res)) {
            if ($is_stage2bid == 0) {
                Toutiao::query()
                    ->whereIn('plan_id', $res['data']['ad_ids'])
                    ->update(['bid' => $bid]);
            } elseif ($is_stage2bid == 1) {
                Toutiao::query()
                    ->whereIn('plan_id', $res['data']['ad_ids'])
                    ->update(['cpa_bid' => $bid]);
            }
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /**
     * 收集本地头条广告数据
     *
     * @return Array
     */
    public function collectPlanData()
    {
        $group = $this->ad->toutiaoGroup;
        $promote = $this->ad->promote;
        $account = ToutiaoAccount::find($this->ad->promote_id);
        if (false && !$account) {
            BLogger::scope(['toutiao', 'job'])->warning('媒体账号没有关联或激活', $this->descMe());
            return;
        }

        $formBase = [
            'advertiser_id' => $this->advertiser_id, // number, 广告主ID
            'campaign_id' => $group ? $group->campaign_id : '', // string, 广告组ID
            'budget_mode' => $this->toutiao->budget_mode, // string, 广告预算类型
            'budget' => $this->toutiao->budget, // number, 广告预算(最低预算100元,单次预算修改幅度不小于100元,日修改预算不超过20次)
            'start_time' => \Carbon\Carbon::parse($this->toutiao->start_time)->format('Y-m-d H:i'), // string
            'end_time' => \Carbon\Carbon::parse($this->toutiao->end_time)->format('Y-m-d H:i'), // string
            'pricing' => $this->toutiao->pricing, // string
            'schedule_type' => $this->toutiao->schedule_type, // string
            'schedule_time' => $this->toutiao->schedule_time, // string
            'flow_control_mode' => $this->toutiao->flow_control_mode, // string
            // 'open_url' => '', // string, 选填
            // 'external_url' => '', // string, 广告落地页链接(当campaign的landing_type=LINK时必填,landing_type=APP时不填)
            'name' => $this->ad->ad_name, // string
            // 'hide_if_converted' => '', // string, 选填
        ];

        if ($group->landing_type === 'APP') {
            $formBase['app_type'] = $this->toutiao->app_type; // string
            $formBase['download_type'] = "DOWNLOAD_URL"; // string
            switch ($formBase['app_type']) {
                case 'APP_ANDROID':
                    $formBase['download_url'] = $this->ad->down_addr; // string, 广告应用下载链接(当campaign的landing_type=APP时必填,landing_type=LINK时不填)
                    $formBase['package'] = $this->ad->game->bundleid; // string, 广告应用下载包名(应用下载类型，必填)
                    break;
                case 'APP_IOS':
                    $formBase['download_url'] = $this->ad->game_download_url; // string, 广告应用下载链接(当campaign的landing_type=APP时必填,landing_type=LINK时不填)
                    $formBase['package'] = $this->ad->game->bundleid; // string, 广告应用下载包名(应用下载类型，必填)
                    break;
                default:
                    break;
            }
        }

        // OCPC、OCPM 出价方式
        if (in_array($formBase['pricing'], array('PRICING_OCPC', 'PRICING_OCPM'))) {
            $formBase['cpa_bid'] = $this->toutiao->cpa_bid; // number, ocpc、ocpm广告第二阶段广告出价。对于OCPC和OCPM出价是必填项，CPC和CPM不是必填项。
            $formBase['convert_id'] = doubleval($this->toutiao->convert_id); // number, 转化id，可通过【工具模块-OCPC广告创建转化查询】查询可用id。对于OCPC和OCPM出价是必填项，CPC和CPM不是必填项。
        } else {
            $formBase['bid'] = $this->toutiao->bid; // number, 广告出价
        }

        // 选填
        $formAudience = [
            'retargeting_tags_include' => $this->toutiao->retargeting_tags_include,
            'retargeting_tags_exclude' => $this->toutiao->retargeting_tags_exclude,
            'gender' => $this->toutiao->gender,
            'age' => $this->toutiao->age,
            'android_osv' => $this->toutiao->android_osv,
            // 'ios_osv' => '',
            // 'carrier' => [],
            'ac' => $this->toutiao->ac,
            // 'device_brand' => [],
            // 'article_category' => [],
            // 'activate_type' => [],
            // 'platform' => [],
            'city' => $this->toutiao->city,
            'district' => $this->toutiao->district,
            'location_type' => $this->toutiao->location_type,
            // 'app_behavior_target' => '',
            // 'app_category' => [],
            // 'app_ids' => [],
            // 'product_platform_id' => '',
            // 'external_url_params' => '',
            // 'open_url_params' => '',
            // 'dpa_local_audience' => '',
            // 'include_custom_actions' => [],
            // 'exclude_custom_actions' => [],
        ];

        if (sizeof($this->toutiao->ad_tag) > 0) {
            $formAudience['ad_tag'] = $this->toutiao->ad_tag;
        }

        if (sizeof($this->toutiao->interest_tags) > 0) {
            $formAudience['interest_tags'] = $this->toutiao->interest_tags;
        }

        // 不传city和district两个参数，就表示地域不限
        if ($formAudience['district'] == 'ALL') {
            unset($formAudience['city']);
            unset($formAudience['district']);
        }

        // BLogger::scope(['toutiao', 'job'])->debug([$this->toutiao->toArray(), $group->toArray(), $account, $formBase, $formAudience]);
        return array_merge($formBase, $formAudience);
    }

    /**
     * 推送广告创意
     *
     * @return void
     */
    public function pushCreative()
    {
        $form = $this->collectCreativeData();

        $allCount = $this->ad->materials()->where('creative_id', '<>', '')->withTrashed()->count();

        $isUpdate = false;
        if ($allCount > 0) {
            $isUpdate = true;
        }

        if (sizeof($form['creatives']) > 0) {
            $res = null;
            if ($isUpdate) {
                $form['modify_time'] = $this->toutiao->modify_time;
                $res = $this->sdk->creativeUpdate($form);
            } else {
                $res = $this->sdk->creativeCreate($form);
            }
            // 推送结果
            if ($res && array_key_exists('data', $res)) {
            } else {
                BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
        } else {
            BLogger::scope(['toutiao', 'job'])->notice('推送广告创意无', $this->descMe());
        }

        $this->pushCreativeOptStatus();
    }

    /**
     * 拉取广告创意 ID
     *
     * @return void
     */
    public function pullCreativeID()
    {
        $res = $this->sdk->creativeRead([
            'advertiser_id' => $this->advertiser_id,
            'ad_id' => $this->toutiao->plan_id,
        ]);

        if ($res && array_key_exists('data', $res) && array_key_exists('creatives', $res['data'])) {
            $this->toutiao->modify_time = $res['data']['modify_time'];
            $creatives = collect($res['data']['creatives']);
            $creatives->each(function ($creative) {
                if (array_key_exists('third_party_id', $creative)) {
                    $third_party_id = json_decode($creative['third_party_id'], 1);
                    if ($third_party_id && $third_party_id['material_id']) {
                        AdMaterial::where('id', $third_party_id['material_id'])
                            ->update(['creative_id' => sprintf("%.0f", $creative['creative_id'])]);
                    }
                }
            });
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            if ($res['message'] !== '该广告计划还没有创意') {
                throw new Exception($res['message'] ?: __FUNCTION__);
            }
        }
    }

    /**
     * 推送广告创意状态
     *
     * @return void
     */
    public function pushCreativeOptStatus()
    {
        if (in_array($this->toutiao->status, ['AD_STATUS_AUDIT', 'AD_STATUS_REAUDIT'])) {
            return;
        }
        $enables = [];
        $disables = [];

        $this->ad
            ->materials()
            ->withTrashed()
            ->each(function ($material) use (&$enables, &$disables) {
                if ($material->creative_id && $material->opt_status == 'CREATIVE_STATUS_ENABLE' && !$material->deleted_at) {
                    $enables[] = doubleval($material->creative_id);
                } elseif ($material->creative_id && ($material->deleted_at || $material->opt_status != 'CREATIVE_STATUS_ENABLE')) {
                    $disables[] = doubleval($material->creative_id);
                }
            });

        $res = null;
        if (sizeof($enables) > 0) {
            $res = $this->sdk->creativeUpdateStatus([
                'creative_ids' => $enables,
                'opt_status' => 'enable',
            ]);
            if ($res && array_key_exists('data', $res)) {
            } else {
                BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
                if (!in_array($res['message'], ['新建创意素材,未通过审核,不支持暂停'])) {
                    throw new Exception($res['message'] ?: __FUNCTION__);
                }
            }
        }

        if (sizeof($disables) > 0) {
            $res = $this->sdk->creativeUpdateStatus([
                'creative_ids' => $disables,
                'opt_status' => 'disable',
            ]);
            if ($res && array_key_exists('data', $res)) {
            } else {
                BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
                if (!in_array($res['message'], ['新建创意素材,未通过审核,不支持暂停'])) {
                    throw new Exception($res['message'] ?: __FUNCTION__);
                }
            }
        }
    }

    /**
     * 拉取广告创意状态
     *
     * @return void
     */
    public function pullCreativeOptStatus()
    {
        $creative_ids = $this->materials->map(function ($material) {
            return $material->creative_id;
        })->filter(function ($creative_id) {
            return $creative_id != 0 && $creative_id != '' && $creative_id != '0';
        })->unique()->values()->toArray();

        $res = $this->sdk->creativeGet([
            'advertiser_id' => $this->advertiser_id,
            'ad_id' => $this->toutiao->plan_id,
        ]);
        if ($res && Arr::has($res, 'data.list')) {
            $creatives = collect($res['data']['list']);
            $creatives->each(function ($creative) {
                $ad_material = AdMaterial::where('creative_id', sprintf("%.0f", $creative['creative_id']))
                    ->withTrashed()->first();
                if ($ad_material) {
                    $ad_material->status = isset($creative['status']) ? $creative['status'] : '';
                    $ad_material->opt_status = isset($creative['opt_status']) ? $creative['opt_status'] : '';
                    $ad_material->save();
                }
            });
        } else {
            BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $this->descMe());
            throw new Exception($res['message'] ?: __FUNCTION__);
        }
    }

    /**
     * 收集本地头条创意数据
     *
     * @return Array
     */
    public function collectCreativeData()
    {
        // 同步远程创意
        $this->pullCreativeID();
        $this->pullCreativeOptStatus();
        $formBase = [
            'advertiser_id' => $this->advertiser_id, // number
            'ad_id' => $this->toutiao->plan_id ?: '', // string
            // 'track_url' => '', // string
            // 'action_track_url' => '', // string
            // 'video_play_effective_track_url' => '', // string
            // 'video_play_done_track_url' => '', // string
            // 'video_play_track_url' => '', // string
            'is_comment_disable' => $this->toutiao->is_comment_disable, // int
            'close_video_detail' => $this->toutiao->close_video_detail, // int
            // 'creative_display_mode' => $this->toutiao->creative_display_mode, // string
            'inventory_type' => $this->toutiao->inventory_type, // string[]
            // 'source' => '', // string
            'app_name' => $this->toutiao->app_name, // string
            'web_url' => $this->toutiao->web_url, // string
            'ad_keywords' => $this->toutiao->ad_keywords, // string[]
            'ad_category' => $this->toutiao->ad_category, // int
            // 'third_industry_id' => $this->toutiao->third_industry_id, // int
            // 'advanced_creative_type' => '', // string
            // 'phone_number' => '', // string
            // 'button_text' => '', // string
            // 'form_url' => '', // string
            'creatives' => [], // array
        ];
        if ($this->toutiao->sub_title) {
            $formBase['advanced_creative_title'] = $this->toutiao->sub_title; // string, 副标题
        }

        $formCreatives = [];
        $files = $this->uploadFiles();
        $this->ad
            ->materials()
            ->withTrashed()
            ->each(function ($material) use (&$formCreatives, $files) {
                // 已经删除并且没有同步过的创意
                if ($material->deleted_at && $material->creative_id == '') {
                    return;
                }
                $style = $material->style;
                $materialContent = json_decode($material->content);
                $creative = [
                    'title' => $this->toutiao->title,
                    // 'creative_word_ids' => '', // string[], 动态词包ID, 选填
                    'image_mode' => trim($style->enumerated_value),
                    'third_party_id' => json_encode([
                        'material_id' => $material->id,
                    ]), // string, 创意自定义参数, 选填
                ];

                // 创意素材修改
                if ($material->creative_id) {
                    $creative['creative_id'] = $material->creative_id;
                }

                // 当前创意是否含有标题
                $isTextVaild = false;
                if (array_key_exists('text', $materialContent)) {
                    foreach ($materialContent->text as $key => $text) {
                        if ($isTextVaild) {
                            continue;
                        }
                        $isTextVaild = true;
                        $creative['title'] = $text->value;
                    }
                }
                if (!$isTextVaild) {
                    throw new Exception('创意标题不能为空，请输入标题');
                }

                // 当前创意是否可用
                $isMultiVaild = false;
                if (array_key_exists('img', $materialContent)) {
                    // 可以有多组图片
                    $creative['image_ids'] = [];
                    foreach ($materialContent->img as $key => $img) {
                        $file = $files->filter(function ($item) use ($img) {
                            return $item && in_array($img->id, $item->annex_ids);
                        })->first();
                        if ($file) {
                            $isMultiVaild = true;
                            $creative['image_ids'][] = $file->file_id;
                        }
                    }
                } elseif (array_key_exists('video', $materialContent)) {
                    $isMultiVaild = false;
                    foreach ($materialContent->video as $key => $video) {
                        // 只能有一个视频
                        if ($isMultiVaild) {
                            continue;
                        }
                        if (array_key_exists('video', $video)) {
                            $file = $files->filter(function ($item) use ($video) {
                                return $item && in_array($video->video->id, $item->annex_ids);
                            })->first();
                            if ($file) {
                                $isMultiVaild = true;
                                $creative['video_id'] = $file->file_id;
                            }
                        }
                        if (array_key_exists('video_cover', $video)) {
                            $file = $files->filter(function ($item) use ($video) {
                                return $item && in_array($video->video_cover->id, $item->annex_ids);
                            })->first();
                            if ($file) {
                                $isMultiVaild = true;
                                $creative['image_id'] = $file->file_id;
                            }
                        }
                    }
                }
                if ($isMultiVaild) {
                    $formCreatives[] = $creative;
                }
            });

        $formBase['creatives'] = $formCreatives;

        return $formBase;
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
                $item->signature = md5_file($item->file_path);
                return $item;
            });

        $files = $annexs->map(function ($annex) {
            $file = ToutiaoFile::where('advertiser_id', $this->advertiser_id)
                ->where('signature', $annex->signature)
                ->first();
            if ($file) {
                $annex_ids = $file->annex_ids;
                $annex_ids[] = $annex->annex_id;
                $file->annex_ids = array_unique($annex_ids);
                $file->save();
                return $file;
            } elseif (in_array($annex->file_type, ['png', 'jpg'])) {
                $res = $this->sdk->uploadImg($this->advertiser_id, $annex->file_path, 'UPLOAD_BY_FILE', $annex->signature);
                if ($res && array_key_exists('data', $res)) {
                    $data = $res['data'];
                    $file = ToutiaoFile::create([
                        'type' => 'img',
                        'annex_ids' => [$annex->annex_id],
                        'file_id' => $data['id'],
                        'advertiser_id' => $this->advertiser_id,
                        'url' => $data['url'],
                        'signature' => $data['signature'],
                        'data' => $data,
                    ]);
                    $file->save();
                    return $file;
                } else {
                    BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $annex->toArray());
                    throw new Exception($res['message'] ?: __FUNCTION__);
                }
            } elseif (in_array($annex->file_type, ['mp4', 'mpeg', '3gp', 'avi'])) {
                $res = $this->sdk->uploadVideo($annex->file_path, $annex->signature);
                if ($res && array_key_exists('data', $res)) {
                    $data = $res['data'];
                    $file = ToutiaoFile::create([
                        'type' => 'video',
                        'annex_ids' => [$annex->annex_id],
                        'file_id' => $data['video_id'],
                        'advertiser_id' => $this->advertiser_id,
                        'signature' => $annex->signature,
                        'data' => $data,
                    ]);
                    $file->save();
                    return $file;
                } else {
                    BLogger::scope(['toutiao', 'job'])->warning(__FUNCTION__, $annex->toArray());
                    throw new Exception($res['message'] ?: __FUNCTION__);
                }
            }
        })->filter(function ($item) {
            return $item;
        })->unique('signature');

        return $files;
    }


    /**
     * 拉取数据报表
     *
     */
    public function pullMyReport()
    {
        $creativeAdKv = collect([]);
        if ($this->promote && $this->promote->ads) {
            $this->promote->ads->each(function ($ad) use (&$creativeAdKv) {
                $ad->materials->each(function ($material) use (&$creativeAdKv) {
                    if ($material->creative_id != '') {
                        $creativeAdKv->push([
                            'ad_id' => $material->ad_id,
                            'material_id' => $material->id,
                            'creative_id' => $material->creative_id
                        ]);
                    }
                });
            });
        }
        $materialIds = $creativeAdKv
            ->map(function ($item) {
                return $item['creative_id'];
            })
            ->toArray();

        $query = [
            'start_date' => $this->params['start_date'],
            'end_date' => $this->params['end_date'],
            'creative_ids' => $materialIds
        ];

        if (sizeof($materialIds) > 0) {
            $page = 0;
            do {
                // 每天0点获取昨天的数据
                $query['page'] = ++$page;
                $res = $this->sdk->getCreativeReport($query);
                if ($res['code'] == 0) {
                    $data = $res['data'];
                    foreach ($data['list'] as $report) {
                        $creative_id = sprintf("%.0f", $report['creative_id']);
                        $materialInfo = $creativeAdKv->where('creative_id', $creative_id)->first();
                        $reportDate = \Carbon\Carbon::parse($report['stat_datetime'])->format('Y-m-d');
                        if ($materialInfo) {
                            AdDwMaterial::updateOrCreate([
                                'ad_id' => $materialInfo['ad_id'],
                                'material_id' => $materialInfo['material_id'],
                                'date' => $reportDate,
                            ], [
                                'show' => $report['show'],
                                'click' => $report['click'],
                            ]);
                            ToutiaoDwCreative::updateOrCreate([
                                'ad_id' => $materialInfo['ad_id'],
                                'creative_id' => $creative_id,
                                'dw_date' => $reportDate,
                            ], [
                                'show' => $report['show'],
                                'click' => $report['click'],
                                'cost' => $report['cost'],
                                'convert' => $report['convert'],
                                'data_all' => json_encode($report, JSON_UNESCAPED_UNICODE)
                            ]);
                        }
                    }
                    BLogger::scope(['toutiao', 'lib'])->info(__FUNCTION__, ['success']);
                } else {
                    BLogger::scope(['toutiao', 'lib'])->warning(__FUNCTION__, ['error']);
                }
            } while (isset($res['data']) && isset($res['data']['list']) && sizeof($res['data']['list']) > 0);
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
        BLogger::scope(['toutiao', 'job'])->error(__FUNCTION__, $zone, $this->descMe());
        switch ($zone) {
            case 'PULL_MY_GROUPS':
                break;
            case 'PUSH_GROUP':
                // 同步状态：同步失败
                if ($this->group) {
                    $this->group->distribute_sync = 3;
                    $this->group->distribute_msg = $e->getMessage();
                    $this->group->save();
                }
                break;
            case 'PUSH_AD':
            case 'PUSH_PLAN':
                // 同步状态：同步失败
                if ($this->ad && $this->ad->distribute_sync != 3) {
                    $this->ad->distribute_sync = 3;
                    $this->ad->distribute_msg = $e->getMessage();
                    $this->ad->save();
                    $this->pullPlan(); // 失败回滚计划
                }
                break;
            case 'PUSH_PLAN_BID':
            case 'PUSH_PLAN_BUDGET':
                // 同步状态：同步失败
                if ($this->ad) {
                    $this->ad->distribute_sync = 3;
                    $this->ad->distribute_msg = $e->getMessage();
                    $this->ad->save();
                }
                break;
            default:
                break;
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
