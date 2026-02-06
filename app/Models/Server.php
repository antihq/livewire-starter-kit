<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'public_ip',
        'status',
        'creator_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sshKeys(): BelongsToMany
    {
        return $this->belongsToMany(SshKey::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function databases(): HasMany
    {
        return $this->hasMany(Database::class);
    }

    public function databaseUsers(): HasMany
    {
        return $this->hasMany(DatabaseUser::class);
    }

    public function cronjobs(): HasMany
    {
        return $this->hasMany(Cronjob::class);
    }

    public function daemons(): HasMany
    {
        return $this->hasMany(Daemon::class);
    }

    public function firewallRules(): HasMany
    {
        return $this->hasMany(FirewallRule::class);
    }

    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function markAsProvisioned(): void
    {
        $this->update(['status' => 'provisioned']);
    }

    public function provisionScriptUrl(): string
    {
        return URL::signedRoute('servers.provision-script', ['server' => $this]);
    }

    public function provisionCommand(): HtmlString
    {
        return new HtmlString("wget --no-verbose -O - {$this->provisionScriptUrl()} | bash");
    }

    public function run(\App\Scripts\Script $script, array $options = []): Task
    {
        $options['timeout'] ??= $script->timeout();

        return $this->tasks()->create([
            'team_id' => $this->team_id,
            'name' => $script->name(),
            'user' => $script->sshAs,
            'options' => $options,
            'script' => (string) $script,
            'output' => '',
        ])->run();
    }

    public function runInBackground(\App\Scripts\Script $script, array $options = []): Task
    {
        return $this->tasks()->create([
            'team_id' => $this->team_id,
            'name' => $script->name(),
            'user' => $script->sshAs,
            'options' => $options,
            'script' => (string) $script,
            'output' => '',
        ])->runInBackground();
    }

    public function sshKeyPath(): string
    {
        return $this->team->privateKeyPath();
    }

    public function ipAddress(): string
    {
        return $this->public_ip;
    }

    public function port(): int
    {
        return 22;
    }
}
