<?php

use App\User;
use App\Libraries\BLogger;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

/**
 * 把一个文件夹里的文件全部转码 只能转一次 否则全部变乱码
 * @param string $filename
 */
if (!function_exists('iconv_file')) {
    function iconv_file($filename, $input_encoding = '', $output_encoding = 'UTF-8')
    {
        if (file_exists($filename)) {
            // if (is_dir($filename)) {
            //     foreach (glob("$filename/*") as $key=>$value) {
            //         iconv_file($value);
            //     }
            // } else {
            $contents_before = file_get_contents($filename);
            if ($input_encoding == '') {
                $input_encoding = mb_detect_encoding($contents_before, array('CP936', 'ASCII', 'GBK', 'GB2312', 'UTF-8'));
            }
            if ($input_encoding == 'UTF-8' && $input_encoding == $output_encoding) { // mb_detect_encoding 函数不工作
                return false;
            } else {
                $contents_after = iconv($input_encoding, $output_encoding, $contents_before);
                file_put_contents($filename, $contents_after);
                return true;
            }
            // }
        } else {
            return false;
        }
    }
}

/**
 * 获取设计设列表，设计师可能包含代理商
 *
 * @param array $listFormat 输出列表形式 #: 使用#号分隔设计师类型， 0设计师， 1代理商， nobody 包含无, search 用于搜索
 * @param array $res 设计师列表
 */
if (!function_exists('getDesignerList')) {
    function getDesignerList($listFormat = ['0#', '1#'])
    {
        $res = collect([]);
        $nobody = (object) [
            'value' => 0,
            'label' => '无'
        ];
        if (in_array('nobody', $listFormat)) {
            $res = $res->push($nobody);
        } elseif (in_array('nobody#', $listFormat)) {
            $nobody->value = '0#0';
            $res = $res->push($nobody);
        }

        // 设计师
        if (in_array('0#', $listFormat) || in_array('0', $listFormat)) {
            $select_uid = in_array('0#', $listFormat) ? 'concat(\'0#\', uid)' : 'uid';
            $users = User::query()
                ->selectRaw("{$select_uid} as value, about as label")
                ->get();
            $res = $res->merge($users);
        }

        // 代理商
        if (in_array('1#', $listFormat) || in_array('1', $listFormat)) {
            // 判断登陆用户是否为代理商
            if (starts_with(Auth::getName(), 'login_agency_')) {
                $agent = Auth::user();
                $agent_uid = in_array('1#', $listFormat) ? '1#' . $agent->agent_id : $agent->agent_id;
                if ($agent) {
                    $res = $res->merge([(object) [
                        'value' => $agent_uid,
                        'label' => $agent->agent_name
                    ]]);
                }
            } else {
                $select_uid = in_array('1#', $listFormat) ? 'concat(\'1#\', ww_agent.agent_id)' : 'ww_agent.agent_id';
                $query = App\Models\Agent::query()
                    ->selectRaw("{$select_uid} as value, agent_name as label");
                if (in_array('search', $listFormat)) {
                    $query
                        ->leftJoin("ww_material", function ($leftJoin) {
                            $leftJoin->on('ww_material.designer_id', 'ww_agent.agent_id');
                        })
                        ->where('ww_material.is_agent', 1)
                        ->groupby('designer_id');
                }
                $res = $res->merge($query->get());
            }
        }
        return $res->all();
    }
}

// 数组转对象
if (!function_exists('array2object')) {
    function array2object($array)
    {
        if (is_array($array)) {
            $obj = new StdClass();

            foreach ($array as $key => $val) {
                $obj->$key = $val;
            }
        } else {
            $obj = $array;
        }

        return $obj;
    }
}

// 对象转数组
if (!function_exists('object2array')) {
    function object2array($object)
    {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }
}

/**
 * 根据文件后缀名，获取文件分类类弄，用于上传时，选择存放目录前缀
 *
 * @param string $msg 返回的消息
 * @param boolean $status 是否成功
 */
if (!function_exists('getUploadCategory')) {
    function getUploadCategory($ext)
    {
        $folders = config('constants.upload.folders');
        foreach ($folders as $folder => $exts) {
            if (in_array($ext, $exts)) {
                return $folder;
            }
        }

        if (isset($folders['others'])) {
            return $folders['others'][0];
        } else {
            return 'others';
        }
    }
}

/**
 * 返回json
 *
 * @param string $msg 返回的消息
 * @param boolean $status 是否成功
 */
if (!function_exists('responseJson')) {
    function responseJson($msg, $status = true, $extra = [])
    {
        $status = $status ? 'success' : 'error';
        $arr = array_merge(
            ['result' => $status, 'message' => $msg],
            is_array($extra) ? $extra : []
        );
        return Response::json($arr);
    }
}

/**
 * 写作的时间人性化
 *
 * @param int $time 写作的时间
 * @return string
 */
if (!function_exists('showWriteTime')) {
    function showWriteTime($time)
    {
        $interval = time() - $time;
        $format = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒',
        );
        foreach ($format as $key => $value) {
            $match = floor($interval / (int) $key);
            if (0 != $match) {
                return $match . $value . '前';
            }
        }
        return date('Y-m-d', $time);
    }
}

/**
 * 二维数组的排序
 *
 * @param array $arr 所要排序的数组
 * @param string $keys 以哪个key来做排序
 * @param string $type desc|asc
 */
if (!function_exists('arraySort')) {
    function arraySort($arr, $keys, $type = 'asc')
    {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        $arr = array();
        foreach ($new_array as $key => $val) {
            $arr[] = $val;
        }
        return $arr;
    }
}

/**
 * 适用于url的base64加密
 */
if (!function_exists('base64url_encode')) {
    function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

/**
 * 适用于url的base64解密
 */
if (!function_exists('base64url_decode')) {
    function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

/**
 * 转化 \ 为 /
 *
 * @param    string  $path   路径
 * @return   string  路径
 */
if (!function_exists('dir_path')) {
    function dir_path($path)
    {
        $path = str_replace('\\', '/', $path);
        if (substr($path, -1) != '/') {
            $path = $path . '/';
        }

        return $path;
    }
}

/**
 * 创建目录
 *
 * @param    string  $path   路径
 * @param    string  $mode   属性
 * @return   string  如果已经存在则返回true，否则为flase
 */
if (!function_exists('dir_create')) {
    function dir_create($path, $mode = 0777)
    {
        if (is_dir($path)) {
            return true;
        }

        $ftp_enable = 0;
        $path = dir_path($path);
        $temp = explode('/', $path);
        $cur_dir = '';
        $max = count($temp) - 1;
        for ($i = 0; $i < $max; $i++) {
            $cur_dir .= $temp[$i] . '/';
            if (@is_dir($cur_dir)) {
                continue;
            }

            @mkdir($cur_dir, 0777, true);
            @chmod($cur_dir, 0777);
        }
        return is_dir($path);
    }
}

/**
 * 根据后缀来简单的判断是不是图片
 *
 * @return boolean
 */
if (!function_exists('isImage')) {
    function isImage($ext)
    {
        $imageExt = 'jpg|gif|png|bmp|jpeg';
        if (!in_array($ext, explode('|', $imageExt))) {
            return false;
        }

        return true;
    }
}

/**
 * 跳转平台登录
 * @return [type] [description]
 */
if (!function_exists('loginByGm')) {
    function loginByGm()
    {
        $gm_site_url = config('interface.GM_SITE_URL');
        $return_url = urlencode("http://" . $_SERVER['HTTP_HOST'] . '/login');

        return $gm_site_url . gmAdminDir() . "/index.php?return_url={$return_url}";
    }
}

/**
 * 跳转平台登出
 * @return [type] [description]
 */
if (!function_exists('logoutByGm')) {
    function logoutByGm()
    {
        $gm_site_url = config('interface.GM_SITE_URL');
        $return_url = urlencode("http://" . $_SERVER['HTTP_HOST'] . '/login');
        return $gm_site_url . gmAdminDir() . "/index.php?app=member&act=logout&return_url={$return_url}";
    }
}

if (!function_exists('getVideoInfo')) {
    function getVideoInfo($file)
    {
        ob_start();
        passthru(sprintf('ffmpeg -i "%s" 2>&1', $file));
        $video_info = ob_get_contents();
        ob_end_clean();
        // 使用输出缓冲，获取ffmpeg所有输出内容
        $ret = array();

        // Duration: 00:33:42.64, start: 0.000000, bitrate: 152 kb/s
        if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $video_info, $matches)) {
            $ret['duration'] = $matches[1]; // 视频长度
            $duration = explode(':', $matches[1]);
            $ret['seconds'] = $duration[0] * 3600 + $duration[1] * 60 + $duration[2]; // 转为秒数
            $ret['start'] = $matches[2]; // 开始时间
            $ret['bitrate'] = $matches[3]; // bitrate 码率 单位kb
        }

        // Stream #0:1: Video: rv20 (RV20 / 0x30325652), yuv420p, 352x288, 117 kb/s, 15 fps, 15 tbr, 1k tbn, 1k tbc
        // Stream #0:0(eng): Video: h264 (Baseline) (avc1 / 0x31637661), yuvj420p(pc, smpte170m), 1280x720, 13879 kb/s, SAR 1:1 DAR 16:9, 24.98 fps, 25 tbr, 90k tbn, 180k tbc (default)
        // Stream #0:0(und): Video: h264 (High) (avc1 / 0x31637661), yuv420p, 640x360 [SAR 3999:4000 DAR 1333:750], 560 kb/s, 25 fps, 25 tbr, 25k tbn, 50 tbc (default)
        if (preg_match("/Video: (.*?), (.*?), (.*?), (.*?)[,\s]/", $video_info, $matches)) {
            $ret['vcodec'] = $matches[1]; // 编码格式
            $ret['vformat'] = $matches[2]; // 视频格式
            if (strpos($matches[3], 'x')) {
                $ret['resolution'] = $matches[3]; // 分辨率
            } else {
                $ret['resolution'] = $matches[4]; // 分辨率
            }

            if (strpos($ret['resolution'], ' ')) {
                $tmp = explode(' ', $ret['resolution']);
                $ret['resolution'] = $tmp[0];
            }

            list($width, $height) = explode('x', $ret['resolution']);
            $ret['width'] = $width;
            $ret['height'] = $height;
        }

        // Stream #0:0: Audio: cook (cook / 0x6B6F6F63), 22050 Hz, stereo, fltp, 32 kb/s
        if (preg_match("/Audio: (.*), (\d*) Hz/", $video_info, $matches)) {
            $ret['acodec'] = $matches[1]; // 音频编码
            $ret['asamplerate'] = $matches[2]; // 音频采样频率
        }

        if (isset($ret['seconds']) && isset($ret['start'])) {
            $ret['play_time'] = $ret['seconds'] + $ret['start']; // 实际播放时间
        }

        $ret['size'] = filesize($file); // 视频文件大小
        // $video_info  = iconv('gbk', 'utf8', $video_info);
        return array($ret, $video_info);
    }
}

if (!function_exists('gmInterface')) {
    function gmInterface($action, $data = array())
    {
        $data['_v'] = '1.0';
        $data['action'] = $action;
        $data['time'] = (string) time();
        foreach ($data as $key => $value) {
            $data[$key] = urlencode($value);
        }
        $data['sign'] = getInterfaceSign($data);

        $gm_site_url = config('interface.GM_SITE_URL');
        $url = $gm_site_url . "gateway/index.php";
        try {
            $response = Zttp\Zttp::send('POST', $url, ['verify' => false, 'form_params' => $data]);
            BLogger::scope(['scat', 'info'])->info(__FUNCTION__, ['response' => $response->body()]);
            $body = $response->body();
            $ret = json_decode($body);

            if (empty($ret)) {
                Log::debug($body);
            }

            if (!$ret->status) {
                return false;
            }

            return $ret->data;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('getInterfaceSign')) {
    function getInterfaceSign($data)
    {
        krsort($data);
        reset($data);
        return md5(json_encode($data) . 'url_gm88');
    }
}

if (!function_exists('gmAdminDir')) {
    function gmAdminDir()
    {
        if (config('app.env') == 'production') {
            return 'admin_cooingandwooing_zz39';
        } else {
            return 'admin';
        }
    }
}


if (!function_exists('gmDataSync')) {
    function gmDataSync($data, $model, $id = 0)
    {
        // 获取对应表名
        $tmp = explode('\\', $model);
        $table = strtolower(end($tmp));

        // 获取最后更新时间
        $update_time = '0';
        $query = $model::query();
        if (in_array($table, array('promote', 'media'))) {
            $query->withTrashed();
        }
        $last = $query->orderBy("update_time", "desc")->first();
        if ($last && $last->update_time) {
            $update_time = $last->update_time;
        }

        $sync = gmInterface(
            'promote.data_sync',
            [
                'table' => $table,
                'update_time' => $update_time,
                'data' => json_encode($data->getAttributes(), JSON_UNESCAPED_UNICODE)
            ]
        );

        if ($sync === false) {
            return false;
        }

        $key = "{$table}_id";

        if (!empty($sync->updates)) {
            foreach ($sync->updates as $k => $val) {
                $query = $model::query();
                if (in_array($table, array('promote', 'media'))) {
                    $query->withTrashed();
                }
                $info = $query->find($val->$key);
                $val->update_time = date('Y-m-d H:i:s');
                $val = object2array($val);
                foreach (['created_at', 'updated_at', 'deleted_at'] as $k_unset => $v_unset) {
                    if (isset($val[$v_unset])) {
                        unset($val[$v_unset]);
                    }
                }
                if ($info) {
                    $info->update($val);
                } else {
                    $model::create($val);
                }
            }
        }

        if ($sync->msg) {
            return $sync->msg;
        } elseif (!$sync->id) {
            return false;
        } else {
            $query = $model::query();
            if (in_array($table, array('promote', 'media'))) {
                $query->withTrashed();
            }
            return $sync->msg ? $sync->msg : $query->find($sync->id);
        }
    }
}


if (!function_exists('cdnRefreshUrl')) {
    function cdnRefreshUrl($url)
    {
        if (!$url) {
            return false;
        }

        // 取目录
        $url = substr($url, 0, strrpos($url, '/') + 1);
        $post_data = array(
            "type" => "0",
            "url" => $url
        );

        $parm_string = http_build_query($post_data, '&');

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, 'http://openapi.exclouds.com/contentService/AddRefresh');
        // curl_setopt($ch, CURLOPT_HEADER, false);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:1CABB04172A289AB66C6A1C6AA4F7B90", "Date:" . date("Y-m-d H:i:s")));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $parm_string);
        // $output = curl_exec($ch);
        // curl_close($ch);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://openapi.new1cloud.com/contentService/AddRefresh');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:78792382F3B6803E9EC01B2293D8A656B001C90F64F58FE43CB58E9B45FD23B0", "Date:" . date("Y-m-d H:i:s")));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parm_string);
        $output = curl_exec($ch);
        curl_close($ch);
        // $ret = json_decode($output, true);

        // if ($ret['code'] == '200') {
        //     return true;
        // }
        // return false;
    }
}


/**
 * 素材报表 工具类 从ES获取广告使用图片数量
 * @param string $filename
 */
if (!function_exists('ad_material')) {
    function ad_material($dw_date_start, $dw_date_end, $media_type, $material_type, $designer_id = null)
    {

        $para = [
            'host' => env('ES_HOST'),
            'port' => env('ES_PORT'),
            'scheme' => env('ES_SCHEME'),
            'user' => env('ES_USER'),
            'pass' => env('ES_PASS')
        ];
        $es_hosts = [$para];
        $client = ClientBuilder::create() // Instantiate a new ClientBuilder
            ->setHosts($es_hosts) // Set the hosts
            ->build(); // Build the client object


        $query_arr[] = '{
            "match_all": {}
          }';
        if ($dw_date_start == '' or $dw_date_end == '') return 999;

        if ($dw_date_start != '' and $dw_date_end != '') {
            $gte = strtotime($dw_date_start) * 1000;
            $lte = strtotime($dw_date_end) * 1000;
            $range = '{
                "range": {
                  "add_time": {
                    "gte": @gte,
                    "lte": @lte,
                    "format": "epoch_millis"
                  }
                }
              }';
            $range = str_replace("@gte", $gte, $range);
            $range = str_replace("@lte", $lte, $range);
            $query_arr[] = $range;
        }
        if ($media_type != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "media_type": {
                    "query": "' . $media_type . '"
                  }
                }
              }';
        }
        if ($material_type != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "material_type.keyword": {
                    "query": "' . $material_type . '"
                  }
                }
              }';
        }
        if ($designer_id != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "designer_id": {
                    "query": "' . $designer_id . '"
                  }
                }
              }';
        }

        $must = implode(',', $query_arr);

        $json = '{
            "aggs": {
              "designer_id": {
                "terms": {
                  "field": "designer_id.keyword",
                  "size": 50,
                  "order": {
                    "_count": "desc"
                  }
                },
                "aggs": {
                  "media_type": {
                    "terms": {
                      "field": "media_type.keyword",
                      "size": 50,
                      "order": {
                        "_count": "desc"
                      }
                    },
                    "aggs": {
                      "ad_id": {
                        "terms": {
                          "field": "ad_id.keyword",
                          "size": 50,
                          "order": {
                            "_count": "desc"
                          }
                        },
                        "aggs": {
                            "material_id": {
                              "terms": {
                                "field": "material_id.keyword",
                                "size": 50,
                                "order": {
                                  "_count": "desc"
                                }
                              }
                            }
                          }
                      }
                    }
                  }
                }
              }
            },
            "size": 0,
            "query": {
              "bool": {
                "must": [@must],
                "filter": [],
                "should": [],
                "must_not": []
              }
            }
          }';
        $json = str_replace("@must", $must, $json);
        $params = [
            'index' => 'ad_materials_data',
            'type' => 'doc',
            'body' => $json
        ];
        $results = $client->search($params);
        return $results;
    }
}

/**
 * 素材报表 工具类 从ES获取广告使用图片数量
 * @param string $filename
 */
if (!function_exists('ad_material_annex')) {
    function ad_material_annex($dw_date_start, $dw_date_end, $media_type, $material_type, $designer_id)
    {

        $para = [
            'host' => env('ES_HOST'),
            'port' => env('ES_PORT'),
            'scheme' => env('ES_SCHEME'),
            'user' => env('ES_USER'),
            'pass' => env('ES_PASS')
        ];
        $es_hosts = [$para];
        $client = ClientBuilder::create() // Instantiate a new ClientBuilder
            ->setHosts($es_hosts) // Set the hosts
            ->build(); // Build the client object


        $query_arr[] = '{
            "match_all": {}
          }';
        if ($dw_date_start == '' or $dw_date_end == '') return 999;

        if ($dw_date_start != '' and $dw_date_end != '') {
            $gte = strtotime($dw_date_start) * 1000;
            $lte = strtotime($dw_date_end) * 1000;
            $range = '{
                "range": {
                  "add_time": {
                    "gte": @gte,
                    "lte": @lte,
                    "format": "epoch_millis"
                  }
                }
              }';
            $range = str_replace("@gte", $gte, $range);
            $range = str_replace("@lte", $lte, $range);
            $query_arr[] = $range;
        }
        if ($media_type != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "media_type": {
                    "query": "' . $media_type . '"
                  }
                }
              }';
        }
        if ($material_type != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "material_type.keyword": {
                    "query": "' . $material_type . '"
                  }
                }
              }';
        }
        if ($designer_id != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "designer_id": {
                    "query": "' . $designer_id . '"
                  }
                }
              }';
        }

        $must = implode(',', $query_arr);

        $json = '{
            "aggs": {
              "2": {
                "terms": {
                  "field": "designer_id",
                  "size": 5,
                  "order": {
                    "1": "desc"
                  }
                },
                "aggs": {
                  "1": {
                    "cardinality": {
                      "field": "annex_id.keyword"
                    }
                  },
                  "3": {
                    "terms": {
                      "field": "distribute.keyword",
                      "size": 50,
                      "order": {
                        "1": "desc"
                      }
                    },
                    "aggs": {
                      "1": {
                        "cardinality": {
                          "field": "annex_id.keyword"
                        }
                      },
                      "4": {
                        "terms": {
                          "field": "ad_id.keyword",
                          "size": 50,
                          "order": {
                            "1": "desc"
                          }
                        },
                        "aggs": {
                          "1": {
                            "cardinality": {
                              "field": "annex_id.keyword"
                            }
                          },
                          "5": {
                            "terms": {
                              "field": "annex_id.keyword",
                              "size": 50,
                              "order": {
                                "1": "desc"
                              }
                            },
                            "aggs": {
                              "1": {
                                "cardinality": {
                                  "field": "annex_id.keyword"
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            },
            "size": 0,
            "query": {
              "bool": {
                "must": [@must],
                "filter": [],
                "should": [],
                "must_not": []
              }
            }
          }';
        $json = str_replace("@must", $must, $json);
        //echo $json;
        $params = [
            'index' => 'ad_materials_data_annex',
            'type' => 'doc',
            'body' => $json
        ];
        $results = $client->search($params);
        return $results;
    }
}

/**
 * 素材报表 工具类 从ES获取广告使用图片数量
 * @param string $filename
 */
if (!function_exists('ad_material_annex_by_designer_id')) {
    function ad_material_annex_by_designer_id($dw_date_start, $dw_date_end, $media_type, $material_type, $designer_id)
    {

        $para = [
            'host' => env('ES_HOST'),
            'port' => env('ES_PORT'),
            'scheme' => env('ES_SCHEME'),
            'user' => env('ES_USER'),
            'pass' => env('ES_PASS')
        ];
        $es_hosts = [$para];
        $client = ClientBuilder::create() // Instantiate a new ClientBuilder
            ->setHosts($es_hosts) // Set the hosts
            ->build(); // Build the client object


        $query_arr[] = '{
            "match_all": {}
          }';
        if ($dw_date_start == '' or $dw_date_end == '') return 999;

        if ($dw_date_start != '' and $dw_date_end != '') {
            $gte = strtotime($dw_date_start) * 1000;
            $lte = strtotime($dw_date_end) * 1000;
            $range = '{
                "range": {
                  "add_time": {
                    "gte": @gte,
                    "lte": @lte,
                    "format": "epoch_millis"
                  }
                }
              }';
            $range = str_replace("@gte", $gte, $range);
            $range = str_replace("@lte", $lte, $range);
            $query_arr[] = $range;
        }
        if ($media_type != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "distribute.keyword": {
                    "query": "' . $media_type . '"
                  }
                }
              }';
        }
        if ($material_type != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "material_type.keyword": {
                    "query": "' . $material_type . '"
                  }
                }
              }';
        }
        if ($designer_id != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "designer_id": {
                    "query": "' . $designer_id . '"
                  }
                }
              }';
        }

        $must = implode(',', $query_arr);

        $json = '{
            "version": true,
            "size": 0,
            "aggs" : {
                  "distinct_annex_id" : {
                      "cardinality" : {
                        "field" : "annex_id.keyword",
                        "precision_threshold" : 100 
                      }
                  }
              },
            "sort": [
              {
                "designer_id": {
                  "order": "desc",
                  "unmapped_type": "boolean"
                }
              }
            ],
            "query": {
              "bool": {
                "must": [@must],
                "filter": [],
                "should": [],
                "must_not": [
                    {
                        "match_phrase": {
                          "distribute": {
                            "query": "Normal"
                          }
                        }
                      },
                      {
                        "range": {
                          "amdeleted_at": {
                            "gte": "2018-01-01",
                            "lt": "2019-01-01"
                          }
                        }
                      }
                ]
              }
            }
          }';
        $json = str_replace("@must", $must, $json);
        //echo $json;
        $params = [
            'index' => 'ad_materials_data_annex',
            'type' => 'doc',
            'body' => $json
        ];
        $results = $client->search($params);
        //var_dump($results);

        $count = 0;
        if ($results["hits"]["total"] > 0) {
            $count = $results["aggregations"]["distinct_annex_id"]["value"];
        }
        return $count;
    }
}


/**
 * 素材报表 工具类 从ES获取广告使用的附件数量
 * @param string $filename
 */
if (!function_exists('ad_material_annex_used_designer_all')) {
    function ad_material_annex_used_designer_all($dw_date_start, $dw_date_end, $media_type, $designer_id, $annex_type)
    {


        $para = [
            'host' => env('ES_HOST'),
            'port' => env('ES_PORT'),
            'scheme' => env('ES_SCHEME'),
            'user' => env('ES_USER'),
            'pass' => env('ES_PASS')
        ];
        $es_hosts = [$para];
        $client = ClientBuilder::create() // Instantiate a new ClientBuilder
            ->setHosts($es_hosts) // Set the hosts
            ->build(); // Build the client object


        $query_arr[] = '{
            "match_all": {}
          }';
        if ($dw_date_start == '' or $dw_date_end == '') return 999;

        if ($dw_date_start != '' and $dw_date_end != '') {
            $gte = strtotime($dw_date_start) * 1000;
            $lte = strtotime($dw_date_end) * 1000;
            $range = '{
                "range": {
                  "annex_add_time": {
                    "gte": @gte,
                    "lte": @lte,
                    "format": "epoch_millis"
                  }
                }
              }';
            $range = str_replace("@gte", $gte, $range);
            $range = str_replace("@lte", $lte, $range);
            $query_arr[] = $range;
        }
        if ($media_type != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "distribute.keyword": {
                    "query": "' . $media_type . '"
                  }
                }
              }';
        }
        if ($designer_id != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "designer_id.keyword": {
                    "query": "' . $designer_id . '"
                  }
                }
              }';
        }
        if ($annex_type != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "annex_type": {
                    "query": "' . $annex_type . '"
                  }
                }
              }';
        }

        $query_arr[] = '{
            "exists": {
              "field": "am_id.keyword"
            }
          }';

        $must = implode(',', $query_arr);

        $json = '{
          "aggs": {
            "2": {
              "terms": {
                "field": "designer_id.keyword",
                "size": 50,
                "order": {
                  "1": "desc"
                }
              },
              "aggs": {
                "1": {
                  "cardinality": {
                    "field": "annex_id.keyword"
                  }
                },
                "3": {
                  "terms": {
                    "field": "about.keyword",
                    "size": 50,
                    "order": {
                      "1": "desc"
                    }
                  },
                  "aggs": {
                    "1": {
                      "cardinality": {
                        "field": "annex_id.keyword"
                      }
                    }
                  }
                }
              }
            }
          },
          "size": 0,
          "query": {
            "bool": {
              "must": [@must],
              "filter": [],
              "should": [],
              "must_not": [
                {
                  "match_phrase": {
                    "distribute.keyword": {
                      "query": "Normal"
                    }
                  }
                }
              ]
            }
          }
        }';
        $json = str_replace("@must", $must, $json);
        //echo $json;
        $params = [
            'index' => 'ad_materials_data_annex',
            'type' => 'doc',
            'body' => $json
        ];
        $results = $client->search($params);
        //var_dump($results);

        return $results;
    }
}

/**
 * 素材报表 工具类 从ES获取图片数量
 * @param string $filename
 */
if (!function_exists('ad_material_annex_designer_all')) {
    function ad_material_annex_designer_all($dw_date_start, $dw_date_end, $designer_id, $annex_type)
    {

        $para = [
            'host' => env('ES_HOST'),
            'port' => env('ES_PORT'),
            'scheme' => env('ES_SCHEME'),
            'user' => env('ES_USER'),
            'pass' => env('ES_PASS')
        ];
        $es_hosts = [$para];
        $client = ClientBuilder::create() // Instantiate a new ClientBuilder
            ->setHosts($es_hosts) // Set the hosts
            ->build(); // Build the client object


        $query_arr[] = '{
            "match_all": {}
          }';
        if ($dw_date_start == '' or $dw_date_end == '') return 999;

        if ($dw_date_start != '' and $dw_date_end != '') {
            $gte = strtotime($dw_date_start) * 1000;
            $lte = strtotime($dw_date_end) * 1000;
            $range = '{
                "range": {
                  "annex_add_time": {
                    "gte": @gte,
                    "lte": @lte,
                    "format": "epoch_millis"
                  }
                }
              }';
            $range = str_replace("@gte", $gte, $range);
            $range = str_replace("@lte", $lte, $range);
            $query_arr[] = $range;
        }
        if ($annex_type != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "annex_type": {
                    "query": "' . $annex_type . '"
                  }
                }
              }';
        }
        if ($designer_id != '') {
            $query_arr[] = '{
                "match_phrase": {
                  "designer_id.keyword": {
                    "query": "' . $designer_id . '"
                  }
                }
              }';
        }

        $must = implode(',', $query_arr);

        $json = '{
            "aggs": {
              "2": {
                "terms": {
                  "field": "designer_id.keyword",
                  "size": 50,
                  "order": {
                    "1": "desc"
                  }
                },
                "aggs": {
                  "1": {
                    "cardinality": {
                      "field": "annex_id"
                    }
                  },
                  "3": {
                    "terms": {
                      "field": "about.keyword",
                      "size": 50,
                      "order": {
                        "1": "desc"
                      }
                    },
                    "aggs": {
                      "1": {
                        "cardinality": {
                          "field": "annex_id"
                        }
                      }
                    }
                  }
                }
              }
            },
            "size": 0,
            "query": {
              "bool": {
                "must": [@must],
                "filter": [],
                "should": [],
                "must_not": [
                  {
                    "exists": {
                      "field": "am_id.keyword"
                    }
                  },
                  {
                    "exists": {
                      "field": "annex_deleted_at"
                    }
                  }
                ]
              }
            }
          }';
        $json = str_replace("@must", $must, $json);
        //echo $json;
        $params = [
            'index' => 'ad_materials_data_annex',
            'type' => 'doc',
            'body' => $json
        ];
        $results = $client->search($params);
        //var_dump($results);

        return $results;
    }
}

/**
 * 素材报表 工具类 添加记录到ES 
 * @param string $filename
 */
if (!function_exists('ad_material_syn')) {
    function ad_material_syn($ad_materail_id)
    {

        $para = [
            'host' => env('ES_HOST'),
            'port' => env('ES_PORT'),
            'scheme' => env('ES_SCHEME'),
            'user' => env('ES_USER'),
            'pass' => env('ES_PASS')
        ];
        $es_hosts = [$para];
        $client = ClientBuilder::create() // Instantiate a new ClientBuilder
            ->setHosts($es_hosts) // Set the hosts
            ->build(); // Build the client object

        //1在ad_materials 通过id获取到记录
        $sql = 'select m.id,m.ad_id,m.creative_id,m.material_ids,t.id as t_id,u.id as u_id,t.dw_date as t_dw_date,u.dw_date as u_dw_date,
        m.created_at as amcreated_at,m.updated_at as amupdated_at,m.deleted_at as amdeleted_at,
        a.distribute,a.created_at as acreated_at,a.updated_at as aupdated_at,a.deleted_at as adeleted_at,
        t.created_at as tcreated_at,t.updated_at as tupdated_at,t.deleted_at as tdeleted_at,
        u.created_at as ucreated_at,u.updated_at as uupdated_at,u.deleted_at as udeleted_at
        from `ww_ad_materials` m
        left join ww_ad a on m.ad_id = a.ad_id
        left join ww_dist_toutiao_dw_creatives t on m.creative_id = t.creative_id
        left join ww_dist_uchc_dw_creatives u on m.creative_id = u.creativeId 
        where m.id = ' . $ad_materail_id . '
        order by id desc';

        $res = DB::select($sql);

        //return $res;
        //2然后再根据material获取到具体数据
        //$material_ids =  $res->material_ids;
        $row = (array) $res[0];
        if ($row['material_ids'] != '') {
            $arr = explode(",", $row['material_ids']);
            foreach ($arr as $k => $v) {
                $q = "select m.material_id,m.`type`,m.designer_id,m.add_time,m.last_edit_time,m.deleted_at,a.about,a.phone_mob,a.`status` from ww_material m 
                left join ww_admin a on m.designer_id = a.uid where material_id = $v";
                //var_dump($q);
                $result1 = DB::select($q);
                if (sizeof($result1) < 1) continue;
                $row1 = (array) $result1[0];
                //var_dump($row1);
                //组装需要提交的row
                $params_in["id"] = $row["id"] . '_' . $v; //每条记录的id
                $params_in["material_id"] = $v;
                $params_in["designer_id"] = $row1['designer_id'];
                $params_in["material_type"] = $row1['type'];
                $params_in["add_time"] = $row1['add_time'];
                $params_in["last_edit_time"] = $row1['last_edit_time'] * 1000;
                $params_in["deleted_at"] = $row1['deleted_at'];
                $params_in["about"] = $row1['about'];
                $params_in["phone_mob"] = $row1['phone_mob'];
                $params_in["status"] = $row1['status'];

                //外层数据
                $params_in["ad_id"] = $row['ad_id'];
                $params_in["creative_id"] = $row['creative_id'];
                $params_in["am_id"] = $row['id']; //ww_ad_material
                $params_in["ad_id"] = $row['ad_id'];
                $params_in["material_ids"] = $row['material_ids'];
                $params_in["t_id"] = $row['t_id'];
                $params_in["u_id"] = $row['u_id'];

                $params_in["distribute"] = $row["distribute"];
                //外层时间
                if ($row['amcreated_at'] != '') {
                    $params_in["amcreated_at"] = strtotime($row['amcreated_at']) * 1000;
                } else {
                    $params_in["amcreated_at"] = $row['amcreated_at'];
                }
                if ($row['amupdated_at'] != '') {
                    $params_in["amupdated_at"] = strtotime($row['amupdated_at']) * 1000;
                } else {
                    $params_in["amupdated_at"] = $row['amupdated_at'];
                }
                if ($row['amdeleted_at'] != '') {
                    $params_in["amdeleted_at"] = strtotime($row['amdeleted_at']) * 1000;
                } else {
                    $params_in["amdeleted_at"] = $row['amdeleted_at'];
                }

                if ($row['acreated_at'] != '') {
                    $params_in["acreated_at"] = strtotime($row['acreated_at']) * 1000;
                } else {
                    $params_in["acreated_at"] = $row['acreated_at'];
                }
                if ($row['aupdated_at'] != '') {
                    $params_in["aupdated_at"] = strtotime($row['aupdated_at']) * 1000;
                } else {
                    $params_in["aupdated_at"] = $row['aupdated_at'];
                }
                if ($row['adeleted_at'] != '') {
                    $params_in["adeleted_at"] = strtotime($row['adeleted_at']) * 1000;
                } else {
                    $params_in["adeleted_at"] = $row['adeleted_at'];
                }

                if ($row['tcreated_at'] != '') {
                    $params_in["tcreated_at"] = strtotime($row['tcreated_at']) * 1000;
                } else {
                    $params_in["tcreated_at"] = $row['tcreated_at'];
                }
                if ($row['tupdated_at'] != '') {
                    $params_in["tupdated_at"] = strtotime($row['tupdated_at']) * 1000;
                } else {
                    $params_in["tupdated_at"] = $row['tupdated_at'];
                }
                if ($row['tdeleted_at'] != '') {
                    $params_in["tdeleted_at"] = strtotime($row['tdeleted_at']) * 1000;
                } else {
                    $params_in["tdeleted_at"] = $row['tdeleted_at'];
                }

                if ($row['ucreated_at'] != '') {
                    $params_in["ucreated_at"] = strtotime($row['ucreated_at']) * 1000;
                } else {
                    $params_in["ucreated_at"] = $row['ucreated_at'];
                }
                if ($row['uupdated_at'] != '') {
                    $params_in["uupdated_at"] = strtotime($row['uupdated_at']) * 1000;
                } else {
                    $params_in["uupdated_at"] = $row['uupdated_at'];
                }
                if ($row['udeleted_at'] != '') {
                    $params_in["udeleted_at"] = strtotime($row['udeleted_at']) * 1000;
                } else {
                    $params_in["udeleted_at"] = $row['udeleted_at'];
                }



                if ($params_in["t_id"] == '' or $params_in["u_id"] == '') {
                    $params_in["media_type"] = 'Normal';
                }
                if ($params_in["t_id"] != '') {
                    $params_in["media_type"] = 'Toutiao';
                }
                if ($params_in["u_id"] != '') {
                    $params_in["media_type"] = 'Uchc';
                }

                if ($row['t_dw_date'] != '') {
                    $params_in["dw_date"] = strtotime($row['t_dw_date']) * 1000;
                } else {
                    $params_in["dw_date"] = $row['t_dw_date'];
                }
                if ($row['u_dw_date'] != '') {
                    $params_in["dw_date"] = strtotime($row['u_dw_date']) * 1000;
                } else {
                    $params_in["dw_date"] = $row['u_dw_date'];
                }

                $params_in['@timestamp'] = gmdate("Y-m-d\TH:i:s\Z", time());
                //$params_in["creativeId"]=$row['creativeId'];
                //var_dump($params_in);
                //3导入到ES
                $params = [
                    'index' => 'ad_materials_data',
                    'type' => 'doc',
                    'id' => $params_in["id"],
                    'body' => $params_in
                ];
                $results = $client->index($params);
            }
        }
        return "ad_material_syn...success...";
        //4通过route开放到外部

        //5通过队列导数据

    }
}

/**
 * 素材附件报表 工具类 添加记录到ES 
 * @param string $filename
 */
if (!function_exists('ad_material_annex_syn')) {
    function ad_material_annex_syn($ad_materail_id)
    {

        $para = [
            'host' => env('ES_HOST'),
            'port' => env('ES_PORT'),
            'scheme' => env('ES_SCHEME'),
            'user' => env('ES_USER'),
            'pass' => env('ES_PASS')
        ];
        $es_hosts = [$para];
        $client = ClientBuilder::create() // Instantiate a new ClientBuilder
            ->setHosts($es_hosts) // Set the hosts
            ->build(); // Build the client object

        //1在ad_materials 通过id获取到记录
        $sql = 'select m.id,m.ad_id,m.creative_id,m.annex_ids,m.annex_imgs,m.annex_videos,t.id as t_id,u.id as u_id,t.dw_date as t_dw_date,u.dw_date as u_dw_date,
        m.created_at as amcreated_at,m.updated_at as amupdated_at,m.deleted_at as amdeleted_at,
        a.distribute,a.created_at as acreated_at,a.updated_at as aupdated_at,a.deleted_at as adeleted_at,
        t.created_at as tcreated_at,t.updated_at as tupdated_at,t.deleted_at as tdeleted_at,
        u.created_at as ucreated_at,u.updated_at as uupdated_at,u.deleted_at as udeleted_at
        from `ww_ad_materials` m
        left join ww_ad a on m.ad_id = a.ad_id
        left join ww_dist_toutiao_dw_creatives t on m.creative_id = t.creative_id
        left join ww_dist_uchc_dw_creatives u on m.creative_id = u.creativeId 
        where m.id = ' . $ad_materail_id . '
        order by id desc';

        $res = DB::select($sql);

        //return $res;
        //2然后再根据material获取到具体数据
        //$material_ids =  $res->material_ids;
        $row = (array) $res[0];
        //使用annex_imgs 和 annex_videos替换 annex_ids
        //if($row['annex_ids']!=''){
        if ($row['annex_imgs'] != '' or $row['annex_videos'] != '') {
            $arr_imgs = [];
            $arr_videos = [];
            if ($row['annex_imgs'] != '')
                $arr_imgs = explode(",", $row['annex_imgs']);
            if ($row['annex_videos'] != '')
                $arr_videos = explode(",", $row['annex_videos']);

            $arr = array_merge($arr_imgs, $arr_videos);
            //var_dump($arr);

            foreach ($arr as $k => $v) {
                $q = "select ma.annex_id,ma.file_name,ma.annex_type,ma.add_time as annex_add_time,ma.deleted_at as annex_deleted_at,m.material_id,m.is_agent,m.`type`,m.designer_id,m.add_time,m.last_edit_time,m.deleted_at,a.about,a.phone_mob,a.`status` from ww_material_annex ma
					left join ww_material m on ma.material_id = m.material_id
					left join ww_admin a on m.designer_id = a.uid where annex_id = $v";
                //var_dump($q);
                $result1 = DB::select($q);
                if (sizeof($result1) < 1) continue;
                $row1 = (array) $result1[0];
                //var_dump($row1);
                //组装需要提交的row
                $params_in["id"] = $row["id"] . '_' . $v; //每条记录的id
                $params_in["material_id"] = $row1['material_id'];
                $params_in["is_agent"] = $row1['is_agent'];
                $params_in["designer_id"] = $row1['designer_id'];
                $params_in["material_type"] = $row1['type'];
                $params_in["add_time"] = $row1['add_time'];
                $params_in["last_edit_time"] = $row1['last_edit_time'] * 1000;
                if ($row1['deleted_at'] != '') {
                    $params_in["deleted_at"] = strtotime($row1['deleted_at']) * 1000;
                } else {
                    $params_in["deleted_at"] = $row1['deleted_at'];
                }
                $params_in["about"] = $row1['about'];
                $params_in["phone_mob"] = $row1['phone_mob'];
                $params_in["status"] = $row1['status'];
                //annex附件属性
                $params_in["annex_id"] = $row1['annex_id'];
                $params_in["file_name"] = $row1['file_name'];
                $params_in["annex_type"] = $row1['annex_type'];
                $params_in["annex_add_time"] = $row1['annex_add_time'] * 1000;
                if ($row1['annex_deleted_at'] != '') {
                    $params_in["annex_deleted_at"] = strtotime($row1['annex_deleted_at']) * 1000;
                } else {
                    $params_in["annex_deleted_at"] = $row1['annex_deleted_at'];
                }

                //外层数据
                $params_in["ad_id"] = $row['ad_id'];
                $params_in["creative_id"] = $row['creative_id'];
                $params_in["am_id"] = $row['id']; //ww_ad_material
                $params_in["ad_id"] = $row['ad_id'];
                $params_in["annex_ids"] = $row['annex_ids'];
                $params_in["t_id"] = $row['t_id'];
                $params_in["u_id"] = $row['u_id'];

                $params_in["distribute"] = $row["distribute"];
                //外层时间
                if ($row['amcreated_at'] != '') {
                    $params_in["amcreated_at"] = strtotime($row['amcreated_at']) * 1000;
                } else {
                    $params_in["amcreated_at"] = $row['amcreated_at'];
                }
                if ($row['amupdated_at'] != '') {
                    $params_in["amupdated_at"] = strtotime($row['amupdated_at']) * 1000;
                } else {
                    $params_in["amupdated_at"] = $row['amupdated_at'];
                }
                if ($row['amdeleted_at'] != '') {
                    $params_in["amdeleted_at"] = strtotime($row['amdeleted_at']) * 1000;
                } else {
                    $params_in["amdeleted_at"] = $row['amdeleted_at'];
                }

                if ($row['acreated_at'] != '') {
                    $params_in["acreated_at"] = strtotime($row['acreated_at']) * 1000;
                } else {
                    $params_in["acreated_at"] = $row['acreated_at'];
                }
                if ($row['aupdated_at'] != '') {
                    $params_in["aupdated_at"] = strtotime($row['aupdated_at']) * 1000;
                } else {
                    $params_in["aupdated_at"] = $row['aupdated_at'];
                }
                if ($row['adeleted_at'] != '') {
                    $params_in["adeleted_at"] = strtotime($row['adeleted_at']) * 1000;
                } else {
                    $params_in["adeleted_at"] = $row['adeleted_at'];
                }

                if ($row['tcreated_at'] != '') {
                    $params_in["tcreated_at"] = strtotime($row['tcreated_at']) * 1000;
                } else {
                    $params_in["tcreated_at"] = $row['tcreated_at'];
                }
                if ($row['tupdated_at'] != '') {
                    $params_in["tupdated_at"] = strtotime($row['tupdated_at']) * 1000;
                } else {
                    $params_in["tupdated_at"] = $row['tupdated_at'];
                }
                if ($row['tdeleted_at'] != '') {
                    $params_in["tdeleted_at"] = strtotime($row['tdeleted_at']) * 1000;
                } else {
                    $params_in["tdeleted_at"] = $row['tdeleted_at'];
                }

                if ($row['ucreated_at'] != '') {
                    $params_in["ucreated_at"] = strtotime($row['ucreated_at']) * 1000;
                } else {
                    $params_in["ucreated_at"] = $row['ucreated_at'];
                }
                if ($row['uupdated_at'] != '') {
                    $params_in["uupdated_at"] = strtotime($row['uupdated_at']) * 1000;
                } else {
                    $params_in["uupdated_at"] = $row['uupdated_at'];
                }
                if ($row['udeleted_at'] != '') {
                    $params_in["udeleted_at"] = strtotime($row['udeleted_at']) * 1000;
                } else {
                    $params_in["udeleted_at"] = $row['udeleted_at'];
                }



                if ($params_in["t_id"] == '' or $params_in["u_id"] == '') {
                    $params_in["media_type"] = 'Normal';
                }
                if ($params_in["t_id"] != '') {
                    $params_in["media_type"] = 'Toutiao';
                }
                if ($params_in["u_id"] != '') {
                    $params_in["media_type"] = 'Uchc';
                }

                if ($row['t_dw_date'] != '') {
                    $params["dw_date"] = strtotime($row['t_dw_date']) * 1000;
                } else {
                    $params["dw_date"] = $row['t_dw_date'];
                }
                if ($row['u_dw_date'] != '') {
                    $params["dw_date"] = strtotime($row['u_dw_date']) * 1000;
                } else {
                    $params["dw_date"] = $row['u_dw_date'];
                }

                $params_in['@timestamp'] = gmdate("Y-m-d\TH:i:s\Z", time());
                //$params_in["creativeId"]=$row['creativeId'];
                //var_dump($params_in);
                //3导入到ES
                $params = [
                    'index' => 'ad_materials_data_annex',
                    'type' => 'doc',
                    'id' => $params_in["id"],
                    'body' => $params_in
                ];
                $results = $client->index($params);
            }
        }
        return "ad_material_annex_syn...success...";
        //4通过route开放到外部

        //5通过队列导数据

    }
}

/**
 * 素材附件报表 工具类 添加全部原始记录到ES 
 * @param string $filename
 */
if (!function_exists('ad_material_annex_one_syn')) {
    function ad_material_annex_one_syn($materail_annex_id)
    {

        $para = [
            'host' => env('ES_HOST'),
            'port' => env('ES_PORT'),
            'scheme' => env('ES_SCHEME'),
            'user' => env('ES_USER'),
            'pass' => env('ES_PASS')
        ];
        $es_hosts = [$para];
        $client = ClientBuilder::create() // Instantiate a new ClientBuilder
            ->setHosts($es_hosts) // Set the hosts
            ->build(); // Build the client object

        //1在ad_materials 通过id获取到记录
        $sql = "select ma.annex_id,ma.file_name,ma.annex_type,ma.add_time as annex_add_time,ma.deleted_at as annex_deleted_at,m.material_id,m.is_agent,m.`type`,m.designer_id,m.add_time,m.last_edit_time,m.deleted_at,a.about,a.phone_mob,a.`status` from ww_material_annex ma
					left join ww_material m on ma.material_id = m.material_id
					left join ww_admin a on m.designer_id = a.uid where annex_id = $materail_annex_id";

        $res = DB::select($sql);
        $row1 = (array) $res[0];

        //return $res;
        //2然后再根据material获取到具体数据
        //$material_ids =  $res->material_ids;
        $params_in["id"] = $materail_annex_id; //每条记录的id
        //$params["material_id"]=$v;
        $params_in["designer_id"] = $row1['designer_id'];
        $params_in["material_id"] = $row1['material_id'];
        $params_in["is_agent"] = $row1['is_agent'];
        $params_in["material_type"] = $row1['type'];
        $params["add_time"] = $row1['add_time'];
        $params_in["last_edit_time"] = $row1['last_edit_time'] * 1000;
        if ($row1['deleted_at'] != '') {
            $params_in["deleted_at"] = strtotime($row1['deleted_at']) * 1000;
        } else {
            $params_in["deleted_at"] = $row1['deleted_at'];
        }

        $params_in["about"] = $row1['about'];
        $params_in["phone_mob"] = $row1['phone_mob'];
        $params_in["status"] = $row1['status'];
        //annex附件属性
        $params_in["annex_id"] = $row1['annex_id'];
        $params_in["file_name"] = $row1['file_name'];
        $params_in["annex_type"] = $row1['annex_type'];
        $params_in["annex_add_time"] = $row1['annex_add_time'] * 1000;
        if ($row1['annex_deleted_at'] != '') {
            $params_in["annex_deleted_at"] = strtotime($row1['annex_deleted_at']) * 1000;
        } else {
            $params_in["annex_deleted_at"] = $row1['annex_deleted_at'];
        }

        $params_in['@timestamp'] = gmdate("Y-m-d\TH:i:s\Z", time());
        //$params_in["creativeId"]=$row['creativeId'];
        //var_dump($params_in);
        //3导入到ES
        $params = [
            'index' => 'ad_materials_data_annex',
            'type' => 'doc',
            'id' => $params_in["id"],
            'body' => $params_in
        ];
        $results = $client->index($params);

        return "ad_material_annex_syn...success...";
        //4通过route开放到外部

        //5通过队列导数据

    }
}

if (!function_exists('mfSign()')) {
    /**
     *
     * @return array
     */
    function mfSign(array $parameters = [], int $expiration = 60)
    {
        if ($expiration) $parameters = $parameters + ['expires' => time() + $expiration];
        ksort($parameters);
        return $parameters + [
            'signature' => hash_hmac('sha256', http_build_query($parameters), config('myflex.sign_key')),
        ];
    }
}

if (!function_exists('mfSignCheck()')) {
    /**
     *
     * @return array
     */
    function mfSignCheck(array $parameters = [], $ttl = 60)
    {
        $original = $parameters;
        $signature_orig = '';
        if (array_key_exists('signature', $parameters)) {
            $signature_orig = $original['signature'];
            unset($original['signature']);
        }

        $expires = array_key_exists('expires', $parameters)
            ? $parameters['expires']
            : 0;

        ksort($original);
        $signature = hash_hmac('sha256', http_build_query($original), config('myflex.sign_key'));

        return hash_equals($signature, $signature_orig) &&
            !($expires && time() > $expires + 60);
    }
}
