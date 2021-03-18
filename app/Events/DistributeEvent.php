<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DistributeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $name;
    public $action;
    public $params;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($name, $action, $params)
    {
        $this->name = $name;
        $this->action = $action;
        $this->params = $params;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('distribute-event');
    }
}
