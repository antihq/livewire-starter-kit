<?php

namespace App\Support;

use Illuminate\Support\Facades\Process;

class ProcessRunner
{
    public function run(string $command, int $timeout = 60): ShellResponse
    {
        try {
            $result = Process::timeout($timeout)->run($command);

            return new ShellResponse(
                exitCode: $result->exitCode(),
                output: $result->output(),
                errorOutput: $result->errorOutput(),
                timedOut: false
            );
        } catch (\Illuminate\Process\Exceptions\ProcessTimedOutException $e) {
            return new ShellResponse(
                exitCode: 1,
                output: '',
                errorOutput: $e->getMessage(),
                timedOut: true
            );
        }
    }
}
