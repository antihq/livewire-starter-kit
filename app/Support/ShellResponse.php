<?php

namespace App\Support;

readonly class ShellResponse
{
    public function __construct(
        public int $exitCode,
        public string $output,
        public string $errorOutput,
        public bool $timedOut,
    ) {}

    public function successful(): bool
    {
        return $this->exitCode === 0;
    }
}
