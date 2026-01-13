<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NetworkDevice extends Model
{
    use HasFactory;

    protected $table = 'network_devices';

    protected $fillable = [
        'name',
        'ip_address',
        'community',
        'status',
        'uptime',
    ];
}
