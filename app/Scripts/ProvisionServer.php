<?php

namespace App\Scripts;

use App\Models\Server;

class ProvisionServer extends Script
{
    public function __construct(public Server $server)
    {
        //
    }

    public function name(): string
    {
        return "Provisioning Server ({$this->server->name})";
    }

    public function script(): string
    {
        return view('scripts.provision-server', [
            'server' => $this->server,
        ])->render();
    }
}
