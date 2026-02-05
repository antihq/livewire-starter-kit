<?php

use App\Models\BackupDisk;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|in:s3,ftp,sftp')]
    public string $driver = 's3';

    public string $s3_bucket = '';

    public string $s3_access_key = '';

    public string $s3_secret_key = '';

    public string $s3_region = '';

    public bool $s3_use_path_style_endpoint = false;

    public string $s3_custom_endpoint = '';

    public string $ftp_host = '';

    public string $ftp_username = '';

    public string $ftp_password = '';

    public string $sftp_host = '';

    public string $sftp_username = '';

    public string $sftp_password = '';

    public bool $sftp_use_server_key = false;

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }

    #[Computed]
    public function team()
    {
        return $this->user->currentTeam;
    }

    public function mount(): void
    {
        $this->authorize('create', BackupDisk::class);
    }

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'driver' => 'required|in:s3,ftp,sftp',
        ];

        if ($this->driver === 's3') {
            $rules['s3_bucket'] = 'required|string';
            $rules['s3_access_key'] = 'required|string';
            $rules['s3_secret_key'] = 'required|string';
            $rules['s3_region'] = 'required|string';
        }

        if ($this->driver === 'ftp') {
            $rules['ftp_host'] = 'required|string';
            $rules['ftp_username'] = 'required|string';
            $rules['ftp_password'] = 'required|string';
        }

        if ($this->driver === 'sftp') {
            $rules['sftp_host'] = 'required|string';
            $rules['sftp_username'] = 'required|string';

            if (! $this->sftp_use_server_key) {
                $rules['sftp_password'] = 'required|string';
            }
        }

        return $rules;
    }

    public function create(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'driver' => $this->driver,
        ];

        if ($this->driver === 's3') {
            $data = array_merge($data, [
                's3_bucket' => $this->s3_bucket,
                's3_access_key' => $this->s3_access_key,
                's3_secret_key' => $this->s3_secret_key,
                's3_region' => $this->s3_region,
                's3_use_path_style_endpoint' => $this->s3_use_path_style_endpoint,
                's3_custom_endpoint' => empty($this->s3_custom_endpoint) ? null : $this->s3_custom_endpoint,
            ]);
        }

        if ($this->driver === 'ftp') {
            $data = array_merge($data, [
                'ftp_host' => $this->ftp_host,
                'ftp_username' => $this->ftp_username,
                'ftp_password' => $this->ftp_password,
            ]);
        }

        if ($this->driver === 'sftp') {
            $data = array_merge($data, [
                'sftp_host' => $this->sftp_host,
                'sftp_username' => $this->sftp_username,
                'sftp_password' => $this->sftp_use_server_key ? null : $this->sftp_password,
                'sftp_use_server_key' => $this->sftp_use_server_key,
            ]);
        }

        $this->team->backupDisks()->create(array_merge($data, [
            'creator_id' => $this->user->id,
        ]));

        $this->redirectRoute('backup-disks.index', navigate: true);
    }

    public function updatedDriver(): void
    {
        $this->resetValidation();
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="create" class="space-y-8">
        <flux:heading size="lg">Create Backup Disk</flux:heading>

        <flux:input wire:model="name" label="Name" placeholder="e.g., Production S3" required />

        <flux:radio.group wire:model.live="driver" label="Filesystem Driver">
            <flux:radio value="s3" label="S3" />
            <flux:radio value="ftp" label="FTP" />
            <flux:radio value="sftp" label="SFTP" />
        </flux:radio.group>

        <div wire:show="driver === 's3'" class="space-y-4">
            <flux:heading size="md">S3 Configuration</flux:heading>
            <flux:input wire:model="s3_bucket" label="Bucket" placeholder="e.g., my-backups" />
            <flux:input wire:model="s3_access_key" label="Access Key" />
            <flux:input wire:model="s3_secret_key" label="Secret Key" type="password" />
            <flux:input wire:model="s3_region" label="Region" placeholder="e.g., us-east-1" />
            <flux:checkbox wire:model="s3_use_path_style_endpoint" label="Use path style endpoint" />
            <flux:input wire:model="s3_custom_endpoint" label="Custom Endpoint (Optional)" placeholder="e.g., https://s3.example.com" />
        </div>

        <div wire:show="driver === 'ftp'" class="space-y-4">
            <flux:heading size="md">FTP Configuration</flux:heading>
            <flux:input wire:model="ftp_host" label="Host" placeholder="e.g., ftp.example.com" />
            <flux:input wire:model="ftp_username" label="Username" />
            <flux:input wire:model="ftp_password" label="Password" type="password" />
        </div>

        <div wire:show="driver === 'sftp'" class="space-y-4">
            <flux:heading size="md">SFTP Configuration</flux:heading>
            <flux:input wire:model="sftp_host" label="Host" placeholder="e.g., sftp.example.com" />
            <flux:input wire:model="sftp_username" label="Username" />
            <flux:checkbox wire:model.live="sftp_use_server_key" label="Use server public key instead of password" />
            <flux:input wire:model="sftp_password" wire:show="! sftp_use_server_key" label="Password" type="password" />
        </div>

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('backup-disks.index')" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Create Backup Disk</flux:button>
        </div>
    </form>
</div>
