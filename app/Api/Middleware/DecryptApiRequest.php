<?php

namespace App\Api\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class DecryptApiRequest
{
    /**
     * @var Encrypter
     */
    protected $encrypter;

    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
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
            $content = $this->decrypt($request->getContent());
        } catch (DecryptException $exception) {
            return abort(403);
        }

        return $next($this->putIn($request, $content));
    }

    /**
     * decrypt the content
     * @param string $content
     * @return string
     */
    protected function decrypt(string $content)
    {
        return $this->encrypter->decrypt($content, false);
    }

    /**
     * put the decrypt data into request
     * @param Request $request
     * @param string $content
     * @return Request
     */
    protected function putIn(Request $request, string $content)
    {
        if ($request->getContentType() === 'json') {
            $request->setJson(new ParameterBag((array) json_decode($content, true)));
        } else {
            $request->attributes = new ParameterBag([$request->getContentType() => $content]);
        }

        return $request;
    }
}
