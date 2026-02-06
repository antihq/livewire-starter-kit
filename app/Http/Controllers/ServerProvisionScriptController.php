<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Scripts\AuthorizeServerKey;

class ServerProvisionScriptController extends Controller
{
    public function __invoke(Server $server)
    {
        if ($server->status !== 'pending') {
            abort(404);
        }

        $script = new AuthorizeServerKey($server);

        return response($script->script())
            ->header('Content-Type', 'text/plain');
    }
}
