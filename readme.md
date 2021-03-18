## 怪猫广告系统
广告系统主要包含了素材管理、媒体管理、代理商管理功能，满足美术、市场同学日常工作中对广告素材、媒体、代理商的管理；后期希望能够对接开放了API的媒体的广告后台，实现广告自动同步，简化市场同学工作，提高工作效率。

## 相关的配置

### 队列配置

使用 Redis

.env 文件中修改队列驱动

```
QUEUE_DRIVER=redis
```

.env 文件中修改 Redis 配置信息

```
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

启动队列
```sh
php artisan queue:work --queue=default,toutiao,uchc,material
```

定时任务
```sh
* * * * * php artisan schedule:run >> /dev/null 2>&1
```

### 头条配置

数据查看页面 https://ad.toutiao.com/openapi/appid/list.html

.env 文件中添加

```
TOUTIAO_APP_ID=xxxxxx
TOUTIAO_SECRET=xxxxxx
```

### UC汇川配置

由于部分公共数据接口的请求也需要鉴权，所以配置一个公共的账号，用于稳定地获取公共数据。

数据查看页面 https://ad.toutiao.com/openapi/appid/list.html

.env 文件中添加

```
UCHC_USERNAME=xxxxxx
UCHC_PASSWORD=xxxxxx
UCHC_TOKEN=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

### Elasticsearch 配置

.env 文件中添加

```
ES_HOST=xxx.xxx.xxx.xxx
ES_PORT=9200
ES_USER=xxxxxx
ES_PASS=xxxxxx
```

## php 插件依赖

### 更新&安装依赖

```sh
composer update
compsoer dump-autoload
```

### product

- 图片处理
  [Intervention Image](https://github.com/Intervention/image)

- Zip 包处理
  [Zipper](https://github.com/Chumper/Zipper)

- Laravel 模块化插件
  [Laravel-Modules](https://github.com/nWidart/laravel-modules)

- jwt-auth
  [jwt-auth](https://github.com/tymondesigns/jwt-auth)

  ```sh
  php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
  php artisan jwt:secret
  ```



### development
    composer install --no-dev