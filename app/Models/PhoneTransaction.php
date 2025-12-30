<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneTransaction extends Model
{
    use HasFactory;
    protected $table = 'phone_transactions';

    protected $fillable = [
        'serial_num',
        'issued_to',
        'department',
        'date_issued',
        'issued_by',
        'issued_accessories',
        'it_ack_issued',
        'purch_ack_issued',
        'date_returned',
        'returned_to',
        'returned_accessories',
        'it_ack_returned',
        'purch_ack_returned',
        'remarks'
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class, 'serial_num', 'serial_num');
    }
}
