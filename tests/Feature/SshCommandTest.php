<?php

use App\Support\SecureShellCommand;

it('builds ssh command for script execution', function () {
    $command = SecureShellCommand::forScript('192.168.1.1', 22, '/path/to/key', 'fuse', 'echo "hello"');

    expect($command)->toContain('ssh')
        ->toContain('-i')
        ->toContain('-p 22')
        ->toContain('fuse@192.168.1.1');
});

it('builds scp command for file upload', function () {
    $command = SecureShellCommand::forUpload('192.168.1.1', 22, '/path/to/key', 'fuse', '/local/file.sh', '/remote/file.sh');

    expect($command)->toContain('scp')
        ->toContain('-i')
        ->toContain('-P 22')
        ->toContain('fuse@192.168.1.1:/remote/file.sh');
});
