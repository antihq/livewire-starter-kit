<?php

namespace App\Jobs;

use App\Exceptions\ProvisioningTimeout;
use App\Models\Server;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable as LaravelQueueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProvisionServer implements ShouldQueue
{
    use InteractsWithQueue, LaravelQueueable, Queueable, SerializesModels;

    public int $tries = 40;

    public function __construct(public Server $server)
    {
        //
    }

    public function handle(): void
    {
        if ($this->server->isProvisioned()) {
            $this->delete();
        } elseif ($this->server->olderThan(15)) {
            $this->fail(ProvisioningTimeout::for($this->server));
        } elseif ($this->server->isProvisioning()) {
            $this->release(30);
        } elseif ($this->server->isReadyForProvisioning()) {
            $this->server->runProvisioningScript();
        } else {
            $this->release(30);
        }
    }

    public function failed(Exception $exception): void
    {
        report($exception);
    }
}
