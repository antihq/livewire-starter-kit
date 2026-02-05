<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupDisk extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'driver',
        'creator_id',
        's3_bucket',
        's3_access_key',
        's3_secret_key',
        's3_region',
        's3_use_path_style_endpoint',
        's3_custom_endpoint',
        'ftp_host',
        'ftp_username',
        'ftp_password',
        'sftp_host',
        'sftp_username',
        'sftp_password',
        'sftp_use_server_key',
    ];

    protected $hidden = [
        's3_access_key',
        's3_secret_key',
        'ftp_password',
        'sftp_password',
    ];

    protected function casts(): array
    {
        return [
            's3_access_key' => 'encrypted',
            's3_secret_key' => 'encrypted',
            'ftp_password' => 'encrypted',
            'sftp_password' => 'encrypted',
            's3_use_path_style_endpoint' => 'boolean',
            'sftp_use_server_key' => 'boolean',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
