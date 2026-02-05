<?php

namespace App\Support;

use Illuminate\Support\Str;

class ShellOutput
{
    public string $output = '';

    public function __invoke(string $type, string $line): void
    {
        $this->output .= $line;
    }

    public function __toString(): string
    {
        if (Str::startsWith($this->output, 'Warning:')) {
            $this->output = substr($this->output, strpos($this->output, "\n") + 1);
        }

        return trim($this->output);
    }
}
