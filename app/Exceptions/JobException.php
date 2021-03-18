<?php

namespace App\Exceptions;

use App\Libraries\BLogger;

use Exception;
use Illuminate\Support\Facades\Auth;
use Throwable;

class JobException extends Exception
{
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

    /**
     * Get the default context variables for logging.
     *
     * @return array
     */
    protected function context()
    {
        try {
            return array_filter([
                'userId' => Auth::id(),
                'email' => Auth::user() ? Auth::user()->email : null,
            ]);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return mixed
     *
     * @throws \Exception
     */
    public function report()
    {
        BLogger::scope(['jobs', 'report'])
            ->error($this->getMessage(), array_merge($this->context(), ['exception' => $this]));
    }
}
