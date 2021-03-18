<?php
return [
    'distributes' => [
        'Toutiao' => [
            /*
             * 今日头条广告主id
             * appId,secret
             * */
            'advertiser_id'=>'94286567994',
            'app_id'=>'',
            'secret'=>'',
        ]
    ],

    'settlement'       => ['CPC', 'OCPC', 'CPT', 'CPM', 'OCPM', 'CPA', 'CPS'],
    'agentDataItemlist' => [
        ['val'=>'view_count', 'name' => '访客数', 'order' => 1990], // view_cnt
        ['val'=>'ip_count', 'name' => '独立IP数', 'order' => 1980], // view_cnt
        ['val'=>'click_count', 'name' => '点击数', 'order' => 1970], // click_cnt
        ['val'=>'load_count', 'name' => '页面加载完成次数', 'order' => 1960], // click_cnt
        ['val'=>'down_count', 'name' => '点击下载次数', 'order' => 1950], // down_click_cnt
        ['val'=>'click_down_rate', 'name' => '点击-下载', 'order' => 1940], // down_click_cnt
        ['val'=>'finish_count', 'name' => '下载完成次数', 'order' => 1930], // down_finish_cnt
        ['val'=>'down_finish_rate', 'name' => '下载-完成', 'order' => 1920], // down_finish_cnt
        ['val'=>'add_num', 'name' => '新增', 'order' => 1910], // new_user_cnt
        ['val'=>'active_num', 'name' => '活跃', 'order' => 1900], // active_user_cnt
        ['val'=>'pay_num', 'name' => '付费人数', 'order' => 1890], // pay_cnt
        ['val'=>'pay_total', 'name' => '付费金额', 'order' => 1880], // pay_amount
        ['val'=>'pay_rate', 'name' => '付费率', 'order' => 1870], // pya_rate
        ['val'=>'arpu', 'name' => 'ARPU', 'order' => 1860], // arpu
        ['val'=>'arppu', 'name' => 'ARPPU', 'order' => 1850], // arppu
    ],
    'agent_data_items_arr' => [
        ['val'=>'view_count' ,'name'   => '落地页曝光'], // view_cnt
        ['val'=>'load_count'    ,'name'=> '落地页点击'], // click_cnt
        ['val'=>'ctr'           ,'name'=> 'CTR'], // ctr
        ['val'=>'down_count'    ,'name'=> '点击下载数'], // down_click_cnt
        ['val'=>'finish_count'  ,'name'=> '下载完成数'], // down_finish_cnt
        ['val'=>'add_num'      ,'name' => '新增用户数'], // new_user_cnt
        ['val'=>'active_num'   ,'name' => '活跃用户数'], // active_user_cnt
        ['val'=>'pay_num'      ,'name' => '付费人数'], // pay_cnt
        ['val'=>'pay_total'    ,'name' => '付费金额'], // pay_amount
        ['val'=>'pay_rate'     ,'name' => '付费率'], // pya_rate
        ['val'=>'next_day_left','name' => '次日留存率'], // next_keep_rate
        ['val'=>'arpu'         ,'name' => 'ARPU'], // arpu
        ['val'=>'arppu'        ,'name' => 'ARPPU'], // arppu
    ],
    'agent_data_items' => [
        'view_count'    => '落地页曝光', // view_cnt
        'load_count'    => '落地页点击', // click_cnt
        'ctr'           => 'CTR', // ctr
        'down_count'    => '点击下载数', // down_click_cnt
        'finish_count'  => '下载完成数', // down_finish_cnt
        'add_num'       => '新增用户数', // new_user_cnt
        'active_num'    => '活跃用户数', // active_user_cnt
        'pay_num'       => '付费人数', // pay_cnt
        'pay_total'     => '付费金额', // pay_amount
        'pay_rate'      => '付费率', // pya_rate
        'next_day_left' => '次日留存率', // next_keep_rate
        'arpu'          => 'ARPU', // arpu
        'arppu'         => 'ARPPU', // arppu
    ],
    'game_types'       => [
        '1' => 'APP类',
        '2' => 'HTML5类',
        '3' => 'IOS类',
        '4' => '应用宝类',
        '5' => '阿里类',
        '100' => '微信小程序',
    ],
    'upload'           => [
        'folders'      => [
            'images' => ['jpeg', 'jpg', 'png', 'gif'],
            'videos' => ['mp4'],
            'files'  => ['zip'],
            'others' => ['*'],
        ],
        'unzip_folder' => 'unzip',
    ],
];
