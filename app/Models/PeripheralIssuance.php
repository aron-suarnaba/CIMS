<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeripheralIssuance extends Model
{
    use HasFactory;

    protected $table = 'peripheral_issuances';

    protected $fillable = [
        'serial_num',
        'issued_to',
        'department',
        'date_issued',
        'issued_by',
        'issued_accessories',
        'headphones',
        'charger',
        'acknowledgement',
        'cashout',
        'remarks',
    ];

    protected $casts = [
        'date_issued' => 'date',
        'cashout' => 'boolean',
        'headphones' => 'boolean',
        'charger' => 'boolean',
        'acknowledgement' => 'boolean',
    ];

    public function asset()
    {
        return $this->belongsTo(PeripheralAsset::class, 'serial_num', 'serial_num');
    }

    public function return()
    {
        return $this->hasOne(PeripheralReturn::class, 'peripheral_issuance_id');
    }
}
