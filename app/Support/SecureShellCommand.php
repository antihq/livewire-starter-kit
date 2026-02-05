<?php

namespace App\Support;

class SecureShellCommand
{
    public static function forScript(string $ipAddress, int $port, string $keyPath, string $user, string $script): string
    {
        return sprintf(
            'ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i %s -p %d %s@%s %s',
            escapeshellarg($keyPath),
            $port,
            $user,
            $ipAddress,
            $script
        );
    }

    public static function forUpload(string $ipAddress, int $port, string $keyPath, string $user, string $from, string $to): string
    {
        return sprintf(
            'scp -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o PasswordAuthentication=no -i %s -P %d %s %s@%s:%s',
            escapeshellarg($keyPath),
            $port,
            escapeshellarg($from),
            $user,
            $ipAddress,
            $to
        );
    }
}
