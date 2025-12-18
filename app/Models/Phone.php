<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phone'; // Specifies the table name if it deviates from the pluralized model name (Phone -> phones)

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be set using mass assignment (e.g., Phone::create($data)).
     * Add any fields you intend to fill via forms or APIs here.
     * Note: 'id', 'created_at', and 'updated_at' are automatically handled.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'model',
        'brand',
        'serial_num',
        'ramRom',
        'imei',
        'issued_accessories',
        'with_cashout',
        'issued_to',
        'date_issued',
        'issued_by',
        'issuedAcknowledgementIT',
        'issuedAcknowledgementPurchasing',
        'returned_accessories',
        'returned_by',
        'returned_date',
        'returned_to',
        'returnedAcknowledgementIT',
        'returnedAcknowledgementPurchasing',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * 'date_issued' and 'returned_date' are cast to dates, and boolean fields are cast to boolean.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'with_cashout' => 'boolean',
        'issuedAcknowledgementIT' => 'boolean',
        'issuedAcknowledgementPurchasing' => 'boolean',
        'returnedAcknowledgementIT' => 'boolean',
        'returnedAcknowledgementPurchasing' => 'boolean',
        'date_issued' => 'date',
        'returned_date' => 'date',
    ];

    public function getRouteKeyName()
    {
        return 'serial_num';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'issued_by')
            ->belongsTo(User::class, 'returned_to');
    }
    //  */
}
