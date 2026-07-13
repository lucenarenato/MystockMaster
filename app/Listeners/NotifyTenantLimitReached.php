<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyTenantLimitReached implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event)
    {
        // Implement notification logic here, e.g., sending an email or in-app notification
        \Mail::raw('Seu limite de ' . ucfirst($event->limit) . ' foi atingido. Por favor, verifique seus dados.', function ($message) use ($event) {
            $message->to($event->tenant->email)->subject('Atingiu seu Limite');
        });
    }
}
