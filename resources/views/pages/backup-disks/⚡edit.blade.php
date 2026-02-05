<?php

use App\Models\BackupDisk;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public BackupDisk $backupDisk;

    #[Validate('required|string|max:255')]
    public string $name = '';

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
        $this->authorize('update', $this->backupDisk);

        $this->name = $this->backupDisk->name;

        if ($this->backupDisk->driver === 's3') {
            $this->s3_bucket = $this->backupDisk->s3_bucket ?? '';
            $this->s3_access_key = $this->backupDisk->s3_access_key ?? '';
            $this->s3_secret_key = '';
            $this->s3_region = $this->backupDisk->s3_region ?? '';
            $this->s3_use_path_style_endpoint = $this->backupDisk->s3_use_path_style_endpoint ?? false;
            $this->s3_custom_endpoint = $this->backupDisk->s3_custom_endpoint ?? '';
        }

        if ($this->backupDisk->driver === 'ftp') {
            $this->ftp_host = $this->backupDisk->ftp_host ?? '';
            $this->ftp_username = $this->backupDisk->ftp_username ?? '';
            $this->ftp_password = '';
        }

        if ($this->backupDisk->driver === 'sftp') {
            $this->sftp_host = $this->backupDisk->sftp_host ?? '';
            $this->sftp_username = $this->backupDisk->sftp_username ?? '';
            $this->sftp_password = '';
            $this->sftp_use_server_key = $this->backupDisk->sftp_use_server_key;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];

        if ($this->backupDisk->driver === 's3') {
            $rules['s3_bucket'] = 'required|string';
            $rules['s3_access_key'] = 'required|string';
            $rules['s3_region'] = 'required|string';
        }

        if ($this->backupDisk->driver === 'ftp') {
            $rules['ftp_host'] = 'required|string';
            $rules['ftp_username'] = 'required|string';
        }

        if ($this->backupDisk->driver === 'sftp') {
            $rules['sftp_host'] = 'required|string';
            $rules['sftp_username'] = 'required|string';

            if (! $this->sftp_use_server_key) {
                $rules['sftp_password'] = 'required|string';
            }
        }

        return $rules;
    }

    public function update(): void
    {
        $this->validate();

        $data = ['name' => $this->name];

        if ($this->backupDisk->driver === 's3') {
            $data = array_merge($data, [
                's3_bucket' => $this->s3_bucket,
                's3_access_key' => $this->s3_access_key,
                's3_secret_key' => empty($this->s3_secret_key) ? $this->backupDisk->s3_secret_key : $this->s3_secret_key,
                's3_region' => $this->s3_region,
                's3_use_path_style_endpoint' => $this->s3_use_path_style_endpoint,
                's3_custom_endpoint' => empty($this->s3_custom_endpoint) ? null : $this->s3_custom_endpoint,
            ]);
        }

        if ($this->backupDisk->driver === 'ftp') {
            $data = array_merge($data, [
                'ftp_host' => $this->ftp_host,
                'ftp_username' => $this->ftp_username,
                'ftp_password' => empty($this->ftp_password) ? $this->backupDisk->ftp_password : $this->ftp_password,
            ]);
        }

        if ($this->backupDisk->driver === 'sftp') {
            $data = array_merge($data, [
                'sftp_host' => $this->sftp_host,
                'sftp_username' => $this->sftp_username,
                'sftp_password' => $this->sftp_use_server_key ? null : (empty($this->sftp_password) ? $this->backupDisk->sftp_password : $this->sftp_password),
                'sftp_use_server_key' => $this->sftp_use_server_key,
            ]);
        }

        $this->backupDisk->update($data);

        $this->redirectRoute('backup-disks.index', navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="update" class="space-y-8">
        <flux:heading size="lg">Edit Backup Disk</flux:heading>

        <flux:input wire:model="name" label="Name" required />

        <div class="space-y-4">
            <flux:heading size="md">Driver</flux:heading>
            <flux:text class="uppercase font-semibold text-sm">{{ $backupDisk->driver }}</flux:text>
        </div>

        @if ($backupDisk->driver === 's3')
            <div class="space-y-4">
                <flux:heading size="md">S3 Configuration</flux:heading>
                <flux:input wire:model="s3_bucket" label="Bucket" />
                <flux:input wire:model="s3_access_key" label="Access Key" />
                <flux:input wire:model="s3_secret_key" label="Secret Key" type="password" placeholder="Leave empty to keep current" />
                <flux:input wire:model="s3_region" label="Region" />
                <flux:checkbox wire:model="s3_use_path_style_endpoint" label="Use path style endpoint" />
                <flux:input wire:model="s3_custom_endpoint" label="Custom Endpoint (Optional)" />
            </div>
        @endif

        @if ($backupDisk->driver === 'ftp')
            <div class="space-y-4">
                <flux:heading size="md">FTP Configuration</flux:heading>
                <flux:input wire:model="ftp_host" label="Host" />
                <flux:input wire:model="ftp_username" label="Username" />
                <flux:input wire:model="ftp_password" label="Password" type="password" placeholder="Leave empty to keep current" />
            </div>
        @endif

        @if ($backupDisk->driver === 'sftp')
            <div class="space-y-4">
                <flux:heading size="md">SFTP Configuration</flux:heading>
                <flux:input wire:model="sftp_host" label="Host" />
                <flux:input wire:model="sftp_username" label="Username" />
                <flux:checkbox wire:model.live="sftp_use_server_key" label="Use server public key instead of password" />
                <flux:input wire:model="sftp_password" wire:show="! sftp_use_server_key" label="Password" type="password" placeholder="Leave empty to keep current" />
            </div>
        @endif

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('backup-disks.index')" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Save Changes</flux:button>
        </div>
    </form>
</div>
