<?php

namespace App\Api\Controllers;

use App\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    use ApiResponse;

    /**
     * @var array
     */
    protected static $emptyWhiteList = ['optimizer'];

    /**
     * @var Object
     */
    protected $class;

    /**
     * @var Array
     */
    protected $with;

    // 其他通用的Api帮助函数

    protected function newQuery()
    {
        $builder = $this->class::query();
        if ($this->with) {
            $builder->with($this->with);
        }
        return $builder;
    }

    /**
     * 生成 query search 校验
     *
     * @return \Validator
     */
    // protected function parseSearch($rules = [], $messages = [], $attributes = [])
    // {
    //     $searchRaw = json_decode(Input::get('search'), 1);
    //     $search = is_array($searchRaw)
    //         ? Arr::only($searchRaw, array_keys($rules))
    //         : [];
    //     // 过滤空值
    //     $search = collect($search)->filter(function ($val, $key) {
    //         if (in_array($key, static::$emptyWhiteList)) {
    //             return true;
    //         }
    //         if (is_string($val) && strlen($val) === 0) {
    //             return false;
    //         }
    //         return true;
    //     })->toArray();
    //     return Validator::make($search, $rules, $messages, $attributes);
    // }
    /**
     * 生成 query search 校验
     *
     * @param array $config [
     *      'messages' => [],
     *      'attributes' => [],
     *      '_sort' => [],
     * ]
     *
     * @return \Validator
     */
    protected function parseSearch($rules = [], $config = [])
    {
        if (array_key_exists('_sort', $config) && is_array($config)) {
            $sortFields = $config['_sort']->implode(',');
            $rules = array_merge($rules, [
                '_sort'        => 'nullable|array',
                '_sort.*.k'    => "required|in:{$sortFields}",
                '_sort.*.v'    => 'required|in:ASC,DESC,asc,desc',
            ]);
        }

        $searchRaw = is_array(Input::get('search'))
            ? Input::get('search')
            : json_decode(Input::get('search'), 1);
        $search = is_array($searchRaw)
            ? Arr::only($searchRaw, array_keys($rules))
            : [];
        // 过滤空值
        $search = collect($search)->filter(function ($val, $key) {
            if (in_array($key, static::$emptyWhiteList)) {
                return true;
            }
            if (is_string($val) && strlen($val) === 0) {
                return false;
            }
            return true;
        })->toArray();
        return Validator::make(
            $search,
            $rules,
            Arr::get($config, 'messages', []),
            Arr::get($config, 'attributes', [])
        );
    }

    /**
     * 生成 post data 校验
     *
     * @return \Validator
     */
    protected function parseData($rules = [], $messages = [], $attributes = [])
    {
        $dataRaw = Input::json('data');
        $data = is_array($dataRaw)
            ? Arr::only($dataRaw, array_keys($rules))
            : [];
        // 过滤空值
        $data = collect($data)->filter(function ($val, $key) {
            if (in_array($key, static::$emptyWhiteList)) {
                return true;
            }
            if (is_string($val) && strlen($val) === 0) {
                return false;
            }
            return true;
        })->toArray();
        return Validator::make($data, $rules, $messages, $attributes);
    }
}
