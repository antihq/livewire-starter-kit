<?php

namespace App\Support;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class SecureShellKey
{
    public static function make(?string $password = ''): object
    {
        $name = Str::random(20);
        $tempDir = storage_path('app/ssh-temp');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $keyPath = $tempDir.'/'.$name;
        $publicKeyPath = $keyPath.'.pub';

        Process::run(sprintf(
            'ssh-keygen -C "fuse@antihq.com" -f %s -t rsa -b 4096 -N %s',
            escapeshellarg($keyPath),
            escapeshellarg($password)
        ))->throw();

        $publicKey = file_get_contents($publicKeyPath);
        $privateKey = file_get_contents($keyPath);

        @unlink($keyPath);
        @unlink($publicKeyPath);

        return (object) [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ];
    }
}
