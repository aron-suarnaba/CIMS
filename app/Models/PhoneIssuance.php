<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneIssuance extends Model
{
    use HasFactory;

    protected $table = 'phone_issuances';

    protected $fillable = [
        'serial_num',
        'issued_to',
        'department',
        'date_issued',
        'issued_by',
        'issued_accessories',
        'cashout',
    ];

    protected $casts = [
        'date_issued' => 'date',
        'cashout' => 'boolean',
    ];

    /**
     * Get the phone associated with this issuance
     */
    public function phone()
    {
        return $this->belongsTo(Phone::class, 'serial_num', 'serial_num');
    }

    /**
     * Get the return record for this issuance
     */
    public function return()
    {
        return $this->hasOne(PhoneReturn::class, 'phone_issuance_id');
    }

    /**
     * Check if this issuance has been returned
     */
    public function isReturned()
    {
        return $this->return()->exists();
    }
}
