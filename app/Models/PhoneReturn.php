<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneReturn extends Model
{
    use HasFactory;

    protected $table = 'phone_returns';

    protected $fillable = [
        'phone_issuance_id',
        'date_returned',
        'returned_to',
        'returned_by',
        'returnee_department',
        'returned_accessories',
    ];

    protected $casts = [
        'date_returned' => 'date',
    ];

    /**
     * Get the issuance associated with this return
     */
    public function issuance()
    {
        return $this->belongsTo(PhoneIssuance::class, 'phone_issuance_id');
    }

    /**
     * Get the phone through the issuance
     */
    public function phone()
    {
        return $this->through('issuance')->has('phone');
    }
}
