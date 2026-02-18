<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniPc extends Model
{
    use HasFactory;

    protected $table = 'mini_pcs';

    protected $fillable = [
        'manufacturer_model',
        'cpu',
        'ram',
        'rom',
        'mac',
        'ip',
        'name',
        'purchase_date',
        'warranty_expiry',
        'remarks',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    public function issuances()
    {
        return $this->hasMany(MiniPcIssuance::class);
    }
}
