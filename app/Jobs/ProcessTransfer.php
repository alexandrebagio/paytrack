<?php

namespace App\Jobs;

use App\Models\Transfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessTransfer implements ShouldQueue
{
    use Queueable;


    public function __construct(
        private Transfer $transfer
    ) {
        //
    }

    public function handle(): void
    {
        sleep(10);
    }
}
