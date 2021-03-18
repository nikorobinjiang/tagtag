<?php

namespace App\Support;

use App\Libraries\BLogger;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use ZipArchive;

class MfHelper
{
    /**
     * 获取对应 list 指定键值对应的 item
     */
    public static function getListItem(array $list, string $key, string $val, string $field)
    {
        foreach ($list as $item) {
            if (array_key_exists($key, $item) && $item[$key] == $val) {
                if (strlen($field) > 0 && array_key_exists($field, $item)) {
                    return $item[$field];
                } else {
                    return $item;
                }
            }
        }
        return null;
    }

    /**
     * 获取毫秒级时间戳
     */
    public static function getTimeMsec()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    /**
     * 获取文件后缀，当通过 mime 获取不到时，使用文件名后缀
     *
     * @param \Illuminate\Http\UploadedFile $file 文件
     * @return string 文件后缀
     */
    public static function getFileExtension(UploadedFile $file)
    {
        return $file->extension()
            ?: $file->clientExtension();
    }

    /**
     * 获取 hash 文件名，当 file extension 获取不到时，使用文件名后缀补充
     *
     * @param \Illuminate\Http\UploadedFile $file 文件
     * @return string 文件名
     */
    public static function getFileHashName(UploadedFile $file)
    {
        return $file->hashName()
            . ($file->extension()
                ? ''
                : $file->clientExtension());
    }

    /**
     * 根据文件后缀名，获取文件分类类弄，用于上传时，选择存放目录前缀
     *
     * @param string $msg 返回的消息
     * @param boolean $status 是否成功
     */
    public static function getUploadCategory($ext)
    {
        $folders = config('myflex.upload.folders');
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

    /**
     * 绝结值累加
     */
    public static function absSum(array $payload)
    {
        $sum = 0;
        foreach (array_values($payload) as $value) {
            if (!is_numeric($value)) continue;
            $sum += $value;
        }
        return $sum;
    }

    /**
     * 按 date_range 生成 weeks 列表
     *
     * @return array
     */
    public static function transDateRangeByWeek(array $range)
    {
        $from = Carbon::parse($range[0])->startOfWeek();
        $to = Carbon::parse($range[1])->endOfWeek();
        $segs = $from->diffInWeeks($to);

        $res = [];
        for ($i = 0; $i <= $segs; $i++) {
            $cur = $from->clone()->addWeek($i)->startOfWeek();
            $res[] = [
                $cur->format('Y-m-d'),
                $cur->clone()->endOfWeek()->format('Y-m-d'),
            ];
        }

        return $res;
    }

    /**
     * 计算 uday
     */
    public static function getUday($date)
    {
        $dtZero = \Carbon\Carbon::createFromTimestamp(0)->startOfDay();
        return $date instanceof \Carbon\Carbon
            ? $dtZero->diffInDays($date->startOfDay(), false)
            : $dtZero->diffInDays(\Carbon\Carbon::parse($date)->startOfDay(), false);
    }

    /**
     * 计算当天 uday
     */
    public static function getNowUday()
    {
        $now = \Carbon\Carbon::now();
        return static::getUday($now);
    }

    /**
     * 计算 diff in day
     */
    public static function getDiffInDay($from, $to)
    {
        $from = $from instanceof \Carbon\Carbon
            ? $from->startOfDay()
            : \Carbon\Carbon::parse($from)->startOfDay();
        $to = $to instanceof \Carbon\Carbon
            ? $to->startOfDay()
            : \Carbon\Carbon::parse($to)->startOfDay();

        return $from->diffInDays($to, false);
    }

    /**
     * 计算 diff in day
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function fillQueryOrderBy(\Illuminate\Database\Eloquent\Builder $query, array $sort)
    {
        if (count($sort) > 0) {
            foreach ($sort as $item) {
                $tmp = explode(':', $item);
                if (
                    count($tmp) == 2 && in_array(strtoupper($tmp[1]), ['ASC', 'DESC'])
                ) {
                    $query->orderBy($tmp[0], $tmp[1]);
                }
            }
        }

        return $query;
    }

    /**
     * 进度条
     *
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public static function genProgressBar($max = 0)
    {
        $output = new ConsoleOutput();
        $progressBar = new ProgressBar($output, $max);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        return $progressBar;
    }

    /**
     * 将多维数组拆分成一维数组
     *
     * @return \Illuminate\Support\Collection
     */
    public static function deCompose($payload, $dimensions = null, $common = [])
    {
        if (count($dimensions) > 0) {
            $dimension = array_shift($dimensions);
            $result = [];
            foreach ($payload as $key => $value) {
                $result[] = static::deCompose(
                    $value,
                    $dimensions,
                    array_merge($common, [
                        $dimension => $key
                    ])
                );
            }
            if (count($common) == 0) {
                return collect($result)->flatten(count($dimensions));
            } else {
                return collect($result);
            }
        } else {
            return array_merge($common, $payload);
        }
    }

    /**
     * 批量格式化数字
     *
     * @return array
     */
    public static function batchRound(array $obj, array $fields, int $fix)
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $obj) && is_numeric($obj[$field])) {
                $obj[$field] = round($obj[$field], $fix);
            }
        }

        return $obj;
    }

    /**
     * 通用签名验签
     *
     * @param string $algo 加密算法："md5"，"sha256"
     * @return array
     */
    public static function doSign(array $parameters = [], int $expiration = 60, $algo = 'sha256')
    {
        if ($expiration) $parameters = $parameters + ['expires' => time() + $expiration];
        ksort($parameters);
        $params = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
        return $parameters + [
            'sign' => hash_hmac($algo, $params, config('myflex.sign_key')),
        ];
    }

    /**
     * 通用签名验签
     *
     * @return array
     */
    public static function doSignCheck(array $parameters = [], $algo = 'sha256')
    {
        $original = $parameters;
        $signature_orig = '';
        if (array_key_exists('sign', $parameters)) {
            $signature_orig = $original['sign'];
            unset($original['sign']);
        }

        $expires = array_key_exists('expires', $parameters)
            ? $parameters['expires']
            : 0;

        ksort($original);
        $signature = hash_hmac($algo, http_build_query($original), config('myflex.sign_key'));

        return hash_equals($signature, $signature_orig) &&
            !($expires && time() > $expires + 60);
    }

    /**
     * 通用加密解密
     *
     * @return array
     */
    public static function mfEncrypt(array $payload = [], $fields = [])
    {
        if (count($fields) > 0) {
            $payload['__encrypt'] = [];
            foreach ($fields as $field) {
                if (is_array($payload) && array_key_exists($field, $payload)) {
                    $target = $payload[$field];
                    if ($target instanceof Jsonable) {
                        $payload['__encrypt'][$field] = 'json';
                        $target = $target->toJSON();
                    } else if (is_array($target)) {
                        $payload['__encrypt'][$field] = 'json';
                        $target = json_encode($target);
                    } else {
                        $payload['__encrypt'][$field] = 'default';
                    }

                    $payload[$field] = encrypt($target, false);
                };
            }
        }

        return $payload;
    }

    /**
     * 通用加密解密
     *
     * @return array
     */
    public static function mfDecrypt(array $payload = [])
    {
        if (
            array_key_exists('__encrypt', $payload)
            && is_array($payload['__encrypt'])
            && count($payload['__encrypt']) > 0
        ) {
            foreach ($payload['__encrypt'] as $key => $value) {
                if (!array_key_exists($key, $payload)) continue;
                switch ($value) {
                    case 'json':
                        $payload[$key] = json_decode(decrypt($payload[$key], false), 1);
                        break;
                    default:
                        $payload[$key] = decrypt($payload[$key], false);
                        break;
                }
            }
            unset($payload['__encrypt']);
        }
        return $payload;
    }

    // /**
    //  * 压缩文件并删除原文件
    //  */
    // public static function zipCreate(FilesystemAdapter $storage, string $file_name)
    // {
    //     $file_name_zip = preg_replace('/\.csv$/', '.zip', $file_name);

    //     $zip = new ZipArchive();
    //     $zip->open($storage->path($file_name_zip), ZipArchive::CREATE);
    //     $zip->addFile($storage->path($file_name), basename($file_name));
    //     $zip->close();
    //     $storage->delete($file_name);
    // }

    /**
     * 解压与 zip 同名的 csv 文件
     */
    public static function zipExtra(FilesystemAdapter $storage, string $file_name)
    {
        $csvfile = preg_replace('/\.zip$/', '.csv', $file_name);
        if (!$storage->exists($csvfile)) {
            $csv_basename = basename($csvfile);
            $zip = new ZipArchive();
            if ($zip->open($storage->path($file_name)) === true) {
                $zip->extractTo($storage->path(dirname($csvfile)), [$csv_basename]);
                $zip->close();
            } else {
                return '';
            }
        }
        return $csvfile;
    }

    /**
     * 清理解压出来的csv文件
     */
    public static function zipClear(FilesystemAdapter $storage, string $file_name)
    {
        if (Str::endsWith($file_name, '.csv')) {
            try {
                $storage->delete($file_name);
            } catch (\Throwable $th) {
                BLogger::scope(['cron', 'af'])->error(__FUNCTION__, [
                    'file' => $file_name,
                    'err' => $th,
                ]);
            }
        }
    }



    /**
     * 生成 query search 校验
     *
     * @return \Validator
     */
    public static function parseSearch(
        Request $request,
        array $rules = [],
        array $messages = [],
        array $attributes = [],
        array $emptyWhiteList = []
    ) {
        $searchRaw = json_decode($request->get('search'), 1);
        $search = is_array($searchRaw)
            ? Arr::only($searchRaw, array_keys($rules))
            : [];
        // 过滤空值
        $search = collect($search)
            ->filter(function ($val, $key) use ($emptyWhiteList) {
                if (in_array($key, $emptyWhiteList)) {
                    return true;
                }
                if (is_string($val) && strlen($val) === 0) {
                    return false;
                }
                return true;
            })->toArray();
        return Validator::make($search, $rules, $messages, $attributes);
    }

    /**
     * [TODO]生成 body 校验
     *
     * @return \Validator
     */
    public static function parseBody(
        Request $request,
        array $rules = [],
        array $messages = [],
        array $attributes = [],
        array $emptyWhiteList = []
    ) {
        $payloadRaw = $request->input('data');
        $payload = is_array($payloadRaw)
            ? Arr::only($payloadRaw, array_keys($rules))
            : [];
        // 过滤空值
        $payload = collect($payload)
            ->filter(function ($val, $key) use ($emptyWhiteList) {
                if (in_array($key, $emptyWhiteList)) {
                    return true;
                }
                if (is_string($val) && strlen($val) === 0) {
                    return false;
                }
                return true;
            })->toArray();
        return Validator::make($payload, $rules, $messages, $attributes);
    }
}
