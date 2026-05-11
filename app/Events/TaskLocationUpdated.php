<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type;
    public $id;
    public $latitude;
    public $longitude;
    public $bearing;

    /**
     * Create a new event instance.
     */
    public function __construct($type, $id, $latitude, $longitude, $bearing = null)
    {
        $this->type = $type;
        $this->id = $id;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->bearing = $bearing;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to a private channel for security
        return [
            new PrivateChannel("task.tracking.{$this->type}.{$this->id}"),
        ];
    }


    public function broadcastAs(): string
    {
        return 'location.updated';
    }
}
