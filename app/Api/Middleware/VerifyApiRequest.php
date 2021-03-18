<?php

namespace App\Api\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class VerifyApiRequest
{
    /**
     * @var Encrypter
     */
    protected $encrypter;

    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if ($this->verify($request)) {
                return $next($request);
            } else {
                return abort(403, 'sign failed');
            }
        } catch (DecryptException $exception) {
            return abort(403);
        }

    }

    /**
     * verify the content
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function verify($request)
    {
        $result = false;
        switch ($request->getMethod()) {
            case 'GET':
                $query = $request->query();
                $result = mfSignCheck($query);
                break;
            default:
                $result = true;
                break;
        }

        return $result;
    }
}
