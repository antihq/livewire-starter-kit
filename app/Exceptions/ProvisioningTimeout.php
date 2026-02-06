<?php

namespace App\Exceptions;

use App\Models\Server;
use Exception;

class ProvisioningTimeout extends Exception
{
    public static function for(Server $server): self
    {
        return new self("Server [{$server->name}] provisioning timed out after 15 minutes.");
    }
}
