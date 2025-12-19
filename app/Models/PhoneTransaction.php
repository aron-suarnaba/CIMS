<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneTransaction extends Model
{
    use HasFactory;
    protected $table = 'phone_transactions';

    protected $fillable = [
        'phone_id',
        'issued_to',
        'department',
        'date_issued',
        'issued_by',
        'issued_accessories',
        'it_ack_issued',
        'date_returned',
        'returned_to',
        'returned_accessories',
        'it_ack_returned',
        'remarks'
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
