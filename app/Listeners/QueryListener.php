<?php

namespace App\Listeners;

use App\Libraries\BLogger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $bindings = $event->bindings;
        if (is_array($bindings)) {
            foreach ($bindings as $key => $val) {
                if ((is_numeric($val)) && (strpos($val, '.'))) {
                    $bindings[$key] = num2str($val, 12);
                }
            }
        }
        $sql = str_replace("?", "'%s'", $event->sql);
        $log = vsprintf($sql, $bindings);
        if (in_array(config('app.env'), ['local', 'testing'])) {
            BLogger::scope(['sql', 'query'])->info('SQL', [$log, $event->time]);
        } else {
            if (starts_with($log, "select ")  === false) {
                BLogger::scope(['sql', 'query'])->debug('SQL', [$log, $event->time]);
            }
        }
    }
}
