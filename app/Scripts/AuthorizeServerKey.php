<?php

namespace App\Scripts;

use App\Models\Server;

class AuthorizeServerKey extends Script
{
    public function __construct(public Server $server) {}

    public function name(): string
    {
        return 'Authorize Server Key';
    }

    public function script(): string
    {
        $publicKey = $this->server->team->public_key;

        return view('scripts.authorize-server-key', [
            'server' => $this->server,
            'publicKey' => $publicKey,
        ])->render();
    }

    public function sshAs(): string
    {
        return 'root';
    }

    public function timeout(): int
    {
        return 300;
    }
}
