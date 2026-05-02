<?php

namespace App\Jobs;

use App\Contracts\IOrderCheckHandler;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateOrderCheckJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(IOrderCheckHandler $handler): void
    {
        $handler->generate($this->order);
    }
}
