<?php

namespace App\Api\Traits;

use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Support\Jsonable;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

trait ApiResponse
{
    /**
     * @var int
     */
    protected $statusCode = FoundationResponse::HTTP_OK;

    /**
     * @var Array
     */
    protected $header = [];

    /**
     * @var bool
     */
    protected $encryptSwitch = true;

    /**
     * @var bool
     */
    protected $encryptFields = ['data'];

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @param $data
     * @param array $header
     * @return mixed
     */
    public function respond($data)
    {
        if ($this->encryptSwitch && count($this->encryptFields) > 0) {
            $data['__encrypt'] = [];
            foreach ($this->encryptFields as $key) {
                if (is_array($data) && array_key_exists($key, $data)) {
                    $target = $data[$key];
                    if ($target instanceof Jsonable) {
                        $data['__encrypt'][$key] = 'json';
                        $target = $target->toJson();
                    } else if (is_array($target)) {
                        $data['__encrypt'][$key] = 'json';
                        $target = json_encode($target);
                    } else {
                        $data['__encrypt'][$key] = 'default';
                    }

                    $data[$key] = encrypt($target, false);
                };
            }
        }

        return Response::json($data, $this->getStatusCode(), $this->header);
    }

    /**
     * @param $status
     * @param array $data
     * @param null $code
     * @return mixed
     */
    public function status($status, array $data, $code = null)
    {
        if ($code) {
            $this->setStatusCode($code);
        }

        $status = [
            'status' => $status,
            'code' => $this->statusCode
        ];

        $data = array_merge($status, $data);
        return $this->respond($data);
    }

    /**
     * @param $message
     * @param int $code
     * @param string $status
     * @return mixed
     */
    public function failed($message, $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 'error')
    {
        return $this->setStatusCode($code)->message($message, $status);
    }

    /**
     * @param $message
     * @param string $status
     * @return mixed
     */
    public function toURL($message, $url, $code = FoundationResponse::HTTP_OK, $status = 'redirect')
    {
        return $this->setStatusCode($code)
            ->status($status, [
                'url' => $url,
                'message' => $message
            ]);
    }

    /**
     * @param $message
     * @param string $status
     * @return mixed
     */
    public function message($message, $status = "success")
    {
        return $this->status($status, [
            'message' => $message
        ]);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function internalError($message = "Internal Error!")
    {
        return $this->failed($message, FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function created($message = "created")
    {
        return $this->setStatusCode(FoundationResponse::HTTP_CREATED)
            ->message($message);
    }

    /**
     * @param $data
     * @param string $status
     * @return mixed
     */
    public function success($data, $message = "", $status = "success")
    {
        return $this->status($status, compact('message', 'data'));
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function notFond($message = 'Not Fond!')
    {
        return $this->failed($message, Foundationresponse::HTTP_NOT_FOUND);
    }

    /**
     * 
     */
    public function encrypt($fields, $switch = true)
    {
        $this->encryptFields = $fields;
        $this->encryptSwitch = $switch;
    }
}
