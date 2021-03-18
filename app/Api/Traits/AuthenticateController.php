<?php

namespace App\Api\Traits;

use App\Models\User;

use Validator;
use Socialite;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Passport\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthenticateController extends ApiController
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('auth:api')->only([
            'logout'
        ]);
    }

    public function username()
    {
        return 'username';
    }

    // 登录
    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->only(['username', 'password']), [
    //         'username'    => 'required',
    //         'password' => 'required|between:5,32',
    //     ]);

    //     if ($validator->fails()) {
    //         $request->request->add([
    //             'errors' => $validator->errors()->toArray(),
    //             'code' => 401,
    //         ]);
    //         return $this->sendFailedLoginResponse($request);
    //     }

    //     $credentials = $this->credentials($request);
    //     if ($this->guard('api')->attempt($credentials, true)) {
    //         return $this->sendLoginResponse($request);
    //     }

    //     return $this->setStatusCode(401)->failed('登录失败');
    // }

    // // 个人资料
    // public function info()
    // {
    //     $user = \Auth::guard('api')->user();
    //     $user->roles = ['admin'];
    //     return $this->success($user);
    // }

    // // 退出登录
    // public function logout(Request $request)
    // {
    //     if (\Auth::guard('api')->check()) {
    //         \Auth::guard('api')->user()->token()->revoke();
    //     }

    //     return $this->message('退出登录成功');
    // }

    // 第三方登录
    // public function redirectToProvider($driver)
    // {
    //     if (!in_array($driver, ['qq','wechat'])) {
    //         throw new NotFoundHttpException;
    //     }

    //     return Socialite::driver($driver)->redirect();
    // }

    // 第三方登录回调
    // public function handleProviderCallback($driver)
    // {
    //     $user = Socialite::driver($driver)->user();

    //     $openId = $user->id;

    //     // 第三方认证
    //     $db_user = User::where('xxx', $openId)->first();

    //     if (empty($db_user)) {
    //         $db_user = User::forceCreate([
    //             'username' => '',
    //             'xxUnionId' => $openId,
    //             'nickname' => $user->nickname,
    //             'head' => $user->avatar,
    //         ]);
    //     }

    //     // 直接创建token

    //     $token = $db_user->createToken($openId)->accessToken;

    //     return $this->success(compact('token'));
    // }

    //调用认证接口获取授权码
    protected function authenticateClient(Request $request)
    {
        $credentials = $this->credentials($request);

        $data = $request->all();
        if ($request->refresh_token) {
            $request->request->add([
                'grant_type' => $data['grant_type'],
                'client_id' => $data['client_id'],
                'client_secret' => $data['client_secret'],
                'refresh_token' => $data['refresh_token'],
                'scope' => ''
            ]);
        } else {
            $request->request->add([
                'grant_type' => $data['grant_type'],
                'client_id' => $data['client_id'],
                'client_secret' => $data['client_secret'],
                'email' => $data['email'],
                'password' => $data['password'],
                'scope' => ''
            ]);
        }

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        $response = \Route::dispatch($proxy);
        $content = json_decode($response->content(), 1);
        unset($content['refresh_token']);
        return $this->success($content);
    }

    /*
     *重写 AuthenticatesUsers 部分功能函数来实现整个完整的授权流程
     */
    protected function authenticated(Request $request)
    {
        return $this->authenticateClient($request);
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $msg = $request['errors'];
        $code = $request['code'];
        return $this->setStatusCode($code)->failed($msg);
    }
}
