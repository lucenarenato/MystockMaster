<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenantLimitReached
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tenant;
    public $limit;

    public function __construct($tenant, $limit)
    {
        $this->tenant = $tenant;
        $this->limit = $limit;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->tenant->id);
    }
}
