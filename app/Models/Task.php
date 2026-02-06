<?php

namespace App\Models;

use App\Support\InteractsWithSsh;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory, InteractsWithSsh, MassPrunable;

    const DEFAULT_TIMEOUT = 3600;

    protected $fillable = [
        'team_id',
        'server_id',
        'name',
        'user',
        'status',
        'exit_code',
        'script',
        'output',
        'options',
    ];

    protected $hidden = [
        'options',
        'output',
        'script',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(21));
    }

    public function successful(): bool
    {
        return (int) $this->exit_code === 0;
    }

    public function timeout(): int
    {
        return (int) ($this->options['timeout'] ?? Task::DEFAULT_TIMEOUT);
    }

    public function callbackUrl(): string
    {
        return url()->signedRoute('api.tasks.callback', $this);
    }

    public function finish(int $exitCode = 0): void
    {
        $this->markAsFinished($exitCode);

        $this->update([
            'output' => $this->retrieveOutput(),
        ]);

        foreach ($this->options['then'] ?? [] as $callback) {
            is_object($callback)
                ? $callback->handle($this)
                : app($callback)->handle($this);
        }
    }

    protected function markAsRunning(): self
    {
        return tap($this)->update([
            'status' => 'running',
        ]);
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    protected function markAsTimedOut(string $output = ''): self
    {
        return tap($this)->update([
            'exit_code' => 1,
            'status' => 'timeout',
            'output' => $output,
        ]);
    }

    protected function markAsFinished(int $exitCode = 0, string $output = ''): self
    {
        return tap($this)->update([
            'exit_code' => $exitCode,
            'status' => 'finished',
            'output' => $output,
        ]);
    }
}
