<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class CRUDController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $model = null;

    protected $route_info = null;
    protected $action_info = ''; // 当前请示 action 信息

    protected $action_base = ''; // 当前请示 相对控制器 地址，example: Media\AccountController
    protected $action_name = ''; // 当前请示需要执行的 函数名

    protected $view_base = '';
    protected $view = '';

    protected $rules = ['base'=>[], 'index'=>[], 'store'=>[], 'update'=>[]]; // 请求数据验证
    protected $rules_message = ['base'=>[], 'index'=>[], 'store'=>[], 'update'=>[]]; // 请求数据验证
    protected $validator = null;
    protected $query_id = 0;
    protected $data = [];
    protected $vdata = []; // 传给视图层的数据

    public function __construct()
    {
        $this->route_info = \Request::route();

        // 设置请求 action 信息
        $this->action_info = $this->route_info?$this->route_info->action:'unknow';
        $action_controller_explode = isset($this->action_info['controller'])?explode('@', $this->action_info['controller']):[];
        $baseNamespace = (is_array($this->action_info) && isset($this->action_info['namespace']))?$this->action_info['namespace']:'';
        if (sizeof($this->action_info) > 1) {
            $this->action_base = str_replace($baseNamespace.'\\', '', $action_controller_explode[0]);
            $this->action_name = sizeof($action_controller_explode) > 1?$action_controller_explode[1]:'';
        }

        // 请求数据验证
        if (isset($this->rules[$this->action_name]) && $this->rules[$this->action_name]) {
            $data = \Request::all();
            if (in_array($this->action_name, ['store', 'update', 'show', 'destroy'])) {
                $data = \Request::input('data', []);
                // $data = array_intersect_key($data, $this->rules[$this->action_name]); // 安全性修改，涉及部分验证加强
            }
            $this->validator = Validator::make(
                $data,
                $this->rules[$this->action_name],
                isset($this->rules_message[$this->action_name])?$this->rules_message[$this->action_name]:[]
            );
            $this->data = $this->validator->valid();
            if (!empty($this->data)) {
                foreach ($this->data as $key => $value) {
                    if ($value === null) {
                        $this->data[$key] = '';
                    }
                }
            }
        }

        // view 相关初始化
        $view_split = explode('\\', str_replace_last('Controller', '', $this->action_base));
        $this->view_base = implode('/', array_map('snake_case', $view_split));
        $this->vdata['request_action'] = $this->action_name;
    }

    protected function _vdata()
    {
    }

    protected function _display($view=false)
    {
        // 根据请求类型返回
        if (\Request::wantsJson()) {
            return responseJson('请求成功', true, $this->vdata);
        } else {
            if (!$view) {
                if (view()->exists($this->view_base.'_'.$this->action_name)) {
                    $view = $this->view_base.'_'.$this->action_name;
                } elseif (in_array($this->action_name, array('create', 'edit'))) {
                    $view = $this->view_base.'_form';
                } else {
                    $view = $this->view_base;
                }
            }
            if (!view()->exists($view)) {
                return responseJson('视图文件 '.$view.' 不存在。', false);
            }
            return view($view, $this->vdata);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->vdata['index_action'] = $this->action_base . '@index';
        $this->vdata['create_action'] = $this->action_base . '@create';
        $this->vdata['edit_action'] = $this->action_base . '@edit';
        $this->vdata['destroy_action'] = $this->action_base . '@destroy';
        $this->vdata['show_action'] = $this->action_base . '@show';
        $this->vdata['data'] = $this->data;
        if ($vRes = $this->_vdata()) {
            return $vRes;
        }
        return $this->_display($this->view);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->vdata['form_action'] = $this->action_base . '@store';
        $this->vdata['form_params'] = [];
        $this->vdata['form_method'] = 'POST';
        $this->_vdata();
        return $this->_display($this->view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->validator->fails()) {
            $errors = $this->validator->errors();
            $msg = $errors->first();
            if (substr($msg, 0, 3) == 'The') {
                $msg = '请填写完整信息';
            }
            return responseJson($msg, false, ['errors'=>$errors]);
        } else {
            $insdata = $this->data;

            DB::beginTransaction();
            if (method_exists($this, 'db_commit_start')) {
                $db_commit_start_result = $this->db_commit_start($insdata);
                if ($db_commit_start_result) {
                    DB::rollBack();
                    return responseJson($db_commit_start_result, false, $this->vdata);
                }
            }

            if (in_array($this->model, ['\App\Models\Agent', '\App\Models\Promote', '\App\Models\Media'])) {
                $it = gmDataSync(new $this->model($insdata), $this->model);
                if (!$it) {
                    DB::rollBack();
                    return responseJson('数据同步出错', false);
                } elseif ($it == 'exists') {
                    DB::rollBack();
                    return responseJson('当前数据已存在', false);
                }
            } else {
                $it = $this->model::create($insdata);
            }

            if (method_exists($this, 'db_commit_end')) {
                $db_commit_end_result = $this->db_commit_end($it);
                if ($db_commit_end_result) {
                    DB::rollBack();
                    return responseJson($db_commit_end_result, false, $this->vdata);
                }
            }
            DB::commit();

            if (method_exists($this, 'db_commit_over')) {
                $db_commit_over_result = $this->db_commit_over($it);
                if ($db_commit_over_result) {
                    return responseJson($db_commit_over_result, false, $this->vdata);
                }
            }

            return responseJson('保存成功');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->vdata['id'] = $id;
        // TODO: remove form
        $this->vdata['form_action'] = $this->action_base . '@update';
        $this->vdata['form_params'] = ['id' => $id];
        $this->vdata['form_method'] = 'PUT';
        if (\Request::query('_action') === 'trashed') {
            $this->vdata['it'] = $this->model?$this->model::onlyTrashed()->find($id):null;
        } else {
            $this->vdata['it'] = $this->model?$this->model::find($id):null;
        }

        $this->_vdata();
        return $this->_display($this->view);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->vdata['id'] = $id;
        $this->vdata['form_action'] = $this->action_base . '@update';
        $this->vdata['form_params'] = ['id' => $id];
        $this->vdata['form_method'] = 'PUT';
        $this->vdata['it'] = $this->model?$this->model::find($id):null;

        $this->_vdata();
        return $this->_display($this->view);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($this->validator->fails()) {
            return responseJson('请填写完整信息', false, ['errors'=>$this->validator->errors()]);
        } else {
            $this->query_id = $id;
            $insdata = $this->data;

            $it = $this->model::find($id);

            if (!$it) {
                return responseJson('该记录不存在', false);
            }

            DB::beginTransaction();
            if (method_exists($this, 'db_commit_start')) {
                $db_commit_start_result = $this->db_commit_start($insdata);
                if ($db_commit_start_result) {
                    DB::rollBack();
                    return responseJson($db_commit_start_result, false, $this->vdata);
                }
            }

            if (in_array($this->model, ['\App\Models\Agent', '\App\Models\Promote', '\App\Models\Media'])) {
                $it = gmDataSync(new $this->model($insdata), $this->model, $id);
                if (!$it) {
                    DB::rollBack();
                    return responseJson('数据同步出错', false);
                } elseif ($it == 'exists') {
                    DB::rollBack();
                    return responseJson('当前数据已存在', false);
                }
            } else {
                $it->update($insdata);
            }

            if (method_exists($this, 'db_commit_end')) {
                $db_commit_end_result = $this->db_commit_end($it);
                if ($db_commit_end_result) {
                    DB::rollBack();
                    return responseJson($db_commit_end_result, false, $this->vdata);
                }
            }
            DB::commit();

            if (method_exists($this, 'db_commit_over')) {
                $db_commit_over_result = $this->db_commit_over($it);
                if ($db_commit_over_result) {
                    return responseJson($db_commit_over_result, false, $this->vdata);
                }
            }

            return responseJson('保存成功');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $query = $this->model::query();
        if (isset($this->data['_action']) && $this->data['_action'] == 'restore') {
            $query->onlyTrashed();
        }
        $it = $query->find($id);

        if (!$it) {
            return responseJson('该记录不存在', false);
        }

        DB::beginTransaction();
        if (method_exists($this, 'db_commit_start')) {
            $db_commit_start_result = $this->db_commit_start($it);
            if ($db_commit_start_result) {
                DB::rollBack();
                return responseJson($db_commit_start_result, false, $this->vdata);
            }
        }

        $resMsg = '操作失败';
        $res = false;
        if (isset($this->data['_action']) && $this->data['_action'] == 'restore') {
            $res = $it->restore(); // true|false
            $resMsg = '恢复成功';
        } else {
            $res = $it->delete(); // true|false
            $resMsg = '删除成功';
        }

        if (method_exists($this, 'db_commit_end')) {
            $db_commit_end_result = $this->db_commit_end($it);
            if ($db_commit_end_result) {
                DB::rollBack();
                return responseJson($db_commit_end_result, false, $this->vdata);
            }
        }
        DB::commit();

        if (method_exists($this, 'db_commit_over')) {
            $db_commit_over_result = $this->db_commit_over($it);
            if ($db_commit_over_result) {
                return responseJson($db_commit_over_result, false, $this->vdata);
            }
        }

        return responseJson($resMsg);
    }
}
