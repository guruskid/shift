<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotifyAccountant implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $info;

    public function __construct($info)
    {
        $this->info = $info;

    }

    public function broadcastOn()
    {
        return ['notify'];
    }

    public function broadcastAs()
    {
        // logger('Worked');

        return 'transaction';
    }
}
