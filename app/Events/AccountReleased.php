<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountReleased
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $userId,$message;
    /**
     * Create a new event instance.
     */
    public function __construct($userId, $message = "Your account has been released.")
    {
        $this->userId = $userId;
        $this->message = $message;
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
             new Channel('user.' . $this->userId)
        ];
    }
    public function broadcastAs()
    {
        return 'account.released'; // Event name
    }
}