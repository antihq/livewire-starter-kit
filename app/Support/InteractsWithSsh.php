<?php

namespace App\Support;

use Illuminate\Support\Str;

trait InteractsWithSsh
{
    public function run(): self
    {
        $this->markAsRunning();

        $this->ensureWorkingDirectoryExists();

        try {
            $this->upload();
        } catch (\Illuminate\Process\Exceptions\ProcessTimedOutException $e) {
            return $this->markAsTimedOut();
        }

        return $this->updateForResponse($this->runInline(sprintf(
            'bash %s 2>&1 | tee %s',
            $this->scriptFile(),
            $this->outputFile()
        ), $this->options['timeout'] ?? 60));
    }

    protected function updateForResponse(ShellResponse $response): self
    {
        return tap($this)->update([
            'status' => $response->timedOut ? 'timeout' : 'finished',
            'exit_code' => $response->exitCode,
            'output' => $response->output,
        ]);
    }

    public function runInBackground(): self
    {
        $this->markAsRunning();

        $this->addCallbackToScript();

        $this->ensureWorkingDirectoryExists();

        try {
            $this->upload();
        } catch (\Illuminate\Process\Exceptions\ProcessTimedOutException $e) {
            return $this->markAsTimedOut();
        }

        $sshCommand = SecureShellCommand::forScript(
            $this->server->ipAddress(),
            $this->server->port(),
            $this->server->sshKeyPath(),
            $this->user,
            sprintf('\'nohup bash %s >> %s 2>&1 &\'', $this->scriptFile(), $this->outputFile())
        );

        (new ProcessRunner)->run($sshCommand, 10);

        return $this;
    }

    protected function addCallbackToScript(): void
    {
        $this->update([
            'script' => view('scripts.callback', [
                'task' => $this,
                'path' => str_replace('.sh', '-script.sh', $this->scriptFile()),
                'token' => Str::random(40),
            ])->render(),
        ]);
    }

    protected function ensureWorkingDirectoryExists(): void
    {
        $this->runInline('mkdir -p '.$this->path(), 10);
    }

    protected function upload(): bool
    {
        $result = \Illuminate\Support\Facades\Process::timeout(15)->run(
            SecureShellCommand::forUpload(
                $this->server->ipAddress(),
                $this->server->port(),
                $this->server->sshKeyPath(),
                $this->user,
                $localScript = $this->writeScript(),
                $this->scriptFile()
            )
        );

        $response = new ShellResponse(
            exitCode: $result->exitCode(),
            output: $result->output(),
            errorOutput: $result->errorOutput(),
            timedOut: false
        );

        unlink($localScript);

        $this->server->team->cleanupPrivateKeyPath($this->server->sshKeyPath());

        return $response->exitCode === 0;
    }

    protected function writeScript(): string
    {
        $hash = md5(Str::random(40).$this->script);

        $tempDir = storage_path('app/scripts');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        return tap($tempDir.'/'.$hash, function ($path) {
            file_put_contents($path, $this->script);
        });
    }

    public function retrieveOutput(?string $path = null): string
    {
        return $this->runInline('tail --bytes=2000000 '.($path ?? $this->outputFile()), 10)->output;
    }

    protected function runInline(string $script, int $timeout = 60): ShellResponse
    {
        $token = Str::random(40);

        $sshCommand = SecureShellCommand::forScript(
            $this->server->ipAddress(),
            $this->server->port(),
            $this->server->sshKeyPath(),
            $this->user,
            '\'bash -s \' << \''.$token.'\'
'.$script.'
'.$token
        );

        return (new ProcessRunner)->run($sshCommand, $timeout);
    }

    protected function path(): string
    {
        return '/home/fuse/.cloud';
    }

    protected function scriptFile(): string
    {
        return $this->path().'/'.$this->id.'.sh';
    }

    protected function outputFile(): string
    {
        return $this->path().'/'.$this->id.'.out';
    }
}
