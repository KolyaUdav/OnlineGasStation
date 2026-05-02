<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\GenerateOrderCheckJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateOrderCheck
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        GenerateOrderCheckJob::dispatch($order)->onQueue('high');
    }
}
