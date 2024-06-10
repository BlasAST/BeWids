<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class envioMensaje implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $emisor;
    public $mensaje;
    public $conversacion;
    public $timestamp;
    public $id;
    public function __construct($emisor,$mensaje,$conversacion,$timestamp,$id)
    {
        $this->emisor=$emisor;
        $this->mensaje=$mensaje;
        $this->conversacion=$conversacion;
        $this->timestamp=$timestamp;
        $this->id=$id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    // public function broadcastOn(): array
    // {
        // // new PrivateChannel('chat.' . $this->mensaje['conversacion_id']),
        // return [
            // new PrivateChannel('chat-channel')
        // ];
    // }
    public function broadcastOn()
    {
        return 'chat-channel';
       
    }

    public function broadcastAs()
    {
        return "chat-event";
    }
}
