# ES 行为

- 新加入游戏用户
    - 名称: USER_ADD
    - 附加字段
        | Fields         | Name         |
        | -------------- | :----------: |
        | game_id        | 游戏ID       |
        | promote_id     | 媒体账号ID   |
        | sub_promote_id | 子媒体账号ID |
        | user_id        | 用户ID       |

- 新加入非试玩游戏用户
    - 名称: USER_REG
    - 附加字段
        | Fields         | Name         |
        | -------------- | :----------: |
        | game_id        | 游戏ID       |
        | promote_id     | 媒体账号ID   |
        | sub_promote_id | 子媒体账号ID |
        | user_id        | 用户ID       |
        | phone_mob      |              |
        | open_site      |              |

- 活跃用户
    - 名称: USER_LOGIN
    - 附加字段
        | Fields         | Name         |
        | -------------- | :----------: |
        | game_id        | 游戏ID       |
        | promote_id     | 媒体账号ID   |
        | sub_promote_id | 子媒体账号ID |
        | user_id        | 用户ID       |

- 付费用户
    - 名称: USER_PAY
    - 附加字段
        | Fields         | Name             |
        | -------------- | :--------------: |
        | game_id        | 游戏ID           |
        | promote_id     | 媒体账号ID       |
        | sub_promote_id | 子媒体账号ID     |
        | user_id        | 用户ID           |
        | coins          | 交易的币（实付） |
        | show_coins     | 用户ID           |
        | type           | 付费方式         |
        | remark         | 备注             |

- 新付费用户
    - 说明: 即对应用户只有一条 USER_PAY 记录

- 新注册付费用户
    - 说明: 即对应用户只有一条 USER_PAY 记录，且 USER_REG 时间对应请求查询的时间段

- 付费总计
    - 说明: USER_PAY 对应请求查询的时间段中的 SUM(coins)

- 付费次数
    - 说明: USER_PAY 对应请求查询的时间段中的 COUNT(show_coins)

- 日留存
    - 说明: 前一天新加入游戏的用户在当天还登陆的用户数/前一天新加入游戏的用户数 COUNT(USER_LOGIN) - COUNT(USER_ADD)
    - 计算方式: 递归

- 充值统计
    - 名称: USER_ORDER
    - 附加字段
        | Fields         | Name         |
        | -------------- | :----------: |
        | game_id        | 游戏ID       |
        | promote_id     | 媒体账号ID   |
        | sub_promote_id | 子媒体账号ID |
        | user_id        | 用户ID       |
        | order_amount   | 交易金额     |
        | payment_code   | 交易方式     |
        | status         | 交易状态     |
        | type           | 付费方式     |
        | remark         | 备注         |

- 头条计划单日报表
    - 名称: TOUTIAO_PLAN_REPORT_DAILY
    - 附加字段: TODO