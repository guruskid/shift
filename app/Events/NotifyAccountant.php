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
    public $id;
    public function __construct($info, $id)
    {
        $this->info = $info;
        $this->id = $id;
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
