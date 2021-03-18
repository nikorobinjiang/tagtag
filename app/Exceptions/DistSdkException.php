<?php

namespace App\Exceptions;

use Exception;

class DistSdkException extends Exception
{

    public $remoteCodes;

    public $sdk;

    /**
     * Create a new authentication exception.
     *
     * @param  string  $message
     * @param  array  $guards
     * @return void
     */
    public function __construct($message = 'Empty', $sdk = null)
    {
        parent::__construct($message);
    }

    public function setRemoteCodes($codes)
    {
        $this->remoteCodes = $codes;
        return $this;
    }

    public function getRemoteCodes()
    {
        return $this->remoteCodes;
    }

    public function countRemoteCodes()
    {
        return sizeof($this->remoteCodes);
    }
}
