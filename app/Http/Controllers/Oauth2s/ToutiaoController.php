<?php

namespace App\Http\Controllers\Oauth2s;

use App\Models\Toutiao;
use App\Models\Promote;

use App\Libraries\SdkToutiao;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToutiaoController extends Controller
{
    public function callback(Request $request)
    {
        $sdk = new SdkToutiao();
        $res = $sdk->getAccessTokenWithRequest($request);
        if ($res['code'] == 0) {
            // 拉取远程广告组
            dispatch(new \App\Jobs\JobToutiao('PULL_MY_GROUPS', [
                'promote_id' => $res['data']['promote_id']
            ]));
        }
        if (isset($res['data']) && isset($res['data']['promote_id']) && $res['data']['promote_id']) {
            $promote = Promote::find($res['data']['promote_id']);
            $promote->has_api = 1;
            $promote->save();
        }
        \Request::session()->flash('oauth2Msg', $res);
        return redirect()->route('oauth2_result_index');
    }

    public function redirectAuth(Request $request)
    {
        $code = $request->input('auth_code');

        $res = SdkToutiao::getAccessTokenWithAuthCode($code);
        if ($res['code']==0) {
            $access_token=$res['access_token'];
        }
    }
}
