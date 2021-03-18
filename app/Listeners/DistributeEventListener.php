<?php

namespace App\Listeners;

use App\Events\DistributeEvent;

use App\Models\Promote;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DistributeEventListener implements ShouldQueue
{
    use InteractsWithQueue;

    public $event;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\DistributeEvent  $event
     * @return void
     */
    public function handle(DistributeEvent $event)
    {
        $this->release(10);

        $this->event = $event;

        switch ($event->action) {
            case 'AuthFailed':
                $this->authFailed();
                break;
            case 'AuthRefresh':
                $this->authRefresh();
                break;
            default:
                break;
        }
    }

    // 授权失败
    protected function authFailed()
    {
        $promote = Promote::find($this->event->params['promote_id']);
        if ($promote && $promote->distribute == 'Toutiao' && isset($this->event->params['refresh_token'])) {
            if ($this->event->params['refresh_token'] == $promote->toutiaoAccount->refresh_token) {
                $promote->has_api = 2;
                $promote->save();
            }
        } else if ($promote) {
            $promote->has_api = 2;
            $promote->save();
        }
    }

    // 重新授权检查
    protected function authRefresh()
    {
    }

    /**
     * Handle a job failure.
     *
     * @param  \App\Events\DistributeEvent  $event
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(DistributeEvent $event, $exception)
    {
        $this->delete();
    }
}
