<?php

namespace App\Http\Controllers\Oauth2s;

use App\Libraries\SdkToutiao;
use App\Models\Toutiao;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $oauth2Msg = session('oauth2Msg', false);
        if ($oauth2Msg) {
            return view('common/msg', ['content' => session('oauth2Msg', -1)]);
        } else {
            return redirect('/');
        }
    }
}
