<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CaseNotification implements  ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;

    public function __construct($data)
    {
       
        $this->data = $data;

        Notification::create([
            'user_id' => $this->data['user_id'],
            'message' => $this->data['message'],
            'type' => $this->data['type'],
            'target_id' => $this->data['target_id'],
        ]);

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('popup-channel.' . $this->data['user_id']);

    }

     /**
     * Get the channels the event should broadcastAs as.
     *
     * @return void
     */

    public function broadcastAs(){

        return "case-notification";
    }

}
