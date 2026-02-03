<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ComputerTransaction;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Computers extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'computers';

    protected $fillable = [
        'host_name',
        'serial_number',
        'manufacturer',
        'model',
        'os_version',
        'cpu',
        'ram_gb',
        'storage_gb',
        'mac_address',
        'ip_address',
        'purchase_date',
        'vendor',
        'po_number',
        'warranty_expiry',
        'status',
        'remarks',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date'
    ];

    public function getRouteKeyName()
    {
        return 'host_name';
    }

    public function transactions()
    {
        return $this->hasMany(ComputerTransaction::class, 'host_name', 'host_name');
    }

    public function currentTransaction()
    {
        return $this->hasOne(ComputerTransaction::class, 'host_name', 'host_name')->latestOfMany();
    }

}
