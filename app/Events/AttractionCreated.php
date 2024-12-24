<?php

namespace App\Events;

use App\Models\Attraction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttractionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Attraction $attraction;

    public function __construct(Attraction $attraction)
    {
        $this->attraction = $attraction;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('attractions');
    }

    public function broadcastWith(): array
    {
        return [
            'name' => $this->attraction->name,
            'location' => $this->attraction->location,
        ];
    }

    public function broadcastAs(): string
    {
        return 'attraction.created';
    }
}
