<?php

namespace App\Scripts;

abstract class Script
{
    public string $sshAs = 'fuse';

    abstract public function name(): string;

    abstract public function script(): string;

    public function timeout(): int
    {
        return 3600;
    }

    public function __toString(): string
    {
        return $this->script();
    }
}
