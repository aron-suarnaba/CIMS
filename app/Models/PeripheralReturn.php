<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeripheralReturn extends Model
{
    use HasFactory;

    protected $table = 'peripheral_returns';

    protected $fillable = [
        'peripheral_issuance_id',
        'date_returned',
        'returned_to',
        'returned_by',
        'returnee_department',
        'returned_accessories',
        'charger',
        'headphones',
        'remarks',
    ];

    protected $casts = [
        'date_returned' => 'date',
        'charger' => 'boolean',
        'headphones' => 'boolean',
    ];

    public function issuance()
    {
        return $this->belongsTo(PeripheralIssuance::class, 'peripheral_issuance_id');
    }
}
