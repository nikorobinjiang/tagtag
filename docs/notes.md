账号可用余额=现金可用余额+赠款可用余额

落地页曝光和落地页点击为累加值
点击下载数、下载完成数、新增用户数为累加值
日活跃用户、付费人数的总计值为累加后去重
付费金额为累加值
次日留存不计算总计值

点击-下载 = 点击下载数/独立IP数*100%
下载-完成 =下载完成数/点击下载数*100%

CTR = 总点击/总曝光
ARPU = 付费金额累加值/活跃用户累计值
ARPPU = 付费金额累计值/付费人数
付费率 = 付费人数/活跃用户

## 素材报表

后面广告的数据，是这段时间内有数据的广告，而不是使用了这段时间内上传素材的广告

有可能广告用的是旧的素材

现在做的应该是符合要求的，我怕你重新的时候疏忽了

图片数量,、广告使用图片数量、图片使用率统计时间内的素材上传时间，剩下的统计按报表时间统计

总花费、素材展示数、新增用户数均为零时不展示

1. 视频和图片分开查询
2. 按设计师分组，查询设计师数据汇总
3. 按创意分组，查询单个创意数据
4. 今日头条和uc关联不同的dw表，分别查询
5. 广告数据由创意数据遍历之后分组
6. 新增用户数从母后台接口获取
7. 查询时间：图片/视频总数和已使用数量的按上传时间查询;其他数据按dw表的时间查询;
8. 列表页素材不包含已经删除的广告的关联信息，详情页包含已经删除的广告关联信息
9.  图片数量不包含已经删除的素材，广告使用素材数量包含被删除的广告
10. 数据报表包含已删除的广告数据
11. 图片数量、广告使用图片数量的统计中，只有广告使用图片数量不包括视频封面
12. 列表页报表包含已经删除的广告的关联信息

### 今日头条

转化率 = 转化数/点击数
转化成本 = 花费/转化数

## 广告管理

1. [ ] 创意第一次保存完，后面再编辑就不能删除了
2. [ ] 广告编辑保存后，广告位、素材样式勾选不可修改

## Notes:

创建并同步分发广告后，关闭对应状态
今日头条关闭的是的是计划层级（第二层）
UC头条关闭的是计划层级（第一层）