<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'software_name',
        'vendor',
        'license_type',
        'total_licenses',
        'used_licenses',
        'license_key',
        'purchase_date',
        'expiry_date',
        'status',
        'remarks',
    ];

    protected $appends = [
        'available_licenses',
    ];

    protected $casts = [
        'total_licenses' => 'integer',
        'used_licenses' => 'integer',
        'purchase_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function getAvailableLicensesAttribute(): int
    {
        return max(0, (int) $this->total_licenses - (int) $this->used_licenses);
    }
}
