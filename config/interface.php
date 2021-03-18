<?php

if (env('APP_ENV') == 'production') {
    return [
        "GM_SITE_URL" => 'https://m.gm88.com/', // 官网地址
        "LANDING_PAGE_URL" => 'http://img02.ttfm.gm88.com/', // 落地页地址
        "BI_SITE_URL" => 'http://bi.gm88.com/', // 怪猫游戏数据分析系统地址
    ];
} else {
    return [
        "GM_SITE_URL" => 'https://demo.gm88.com/',
        "LANDING_PAGE_URL" => 'http://img02.demo.gm88.com/', // 落地页地址
        "BI_SITE_URL" => 'http://bi.demo.gm88.com/', // 怪猫游戏数据分析系统地址
    ];
}