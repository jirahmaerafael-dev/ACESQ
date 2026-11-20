<?php

namespace App\Console\Commands;

use App\Services\QueueService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('queue:expire-calls')]
#[Description('Apply grace-period expiry and missed-call penalties to today queues')]
class ExpireQueueCalls extends Command
{
    public function handle(QueueService $queueService): int
    {
        $count = $queueService->expireCalls();
        $this->info("Processed {$count} expired call(s).");

        return self::SUCCESS;
    }
}
