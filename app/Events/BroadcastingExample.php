<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BroadcastingExample extends Event implements ShouldBroadcast
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('pusher-westay');
    }

    public function broadcastWith()
    {
        return ['id' => 'Lumen testing'];
    }

    public function broadcastAs()
    {
        return 'westay-test';
    }
}
