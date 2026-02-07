<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhoneTransaction;

class Phone extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phones'; // Specifies the table name if it deviates from the pluralized model name (Phone -> phones)

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
        'brand',
        'model',
        'serial_num',
        'imei_one',
        'imei_two',
        'ram',
        'rom',
        'sim_no',
        'purchase_date',
        'status',
        'remarks',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * 'date_issued' and 'returned_date' are cast to dates, and boolean fields are cast to boolean.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
    ];

    // Use default id for routing (no custom getRouteKeyName needed)

    /**
     * Get all issuances for this phone
     */
    public function issuances()
    {
        return $this->hasMany(PhoneIssuance::class, 'serial_num', 'serial_num');
    }

    /**
     * Get the current active issuance (without return)
     */
    public function currentIssuance()
    {
        return $this->hasOne(PhoneIssuance::class, 'serial_num', 'serial_num')
            ->whereDoesntHave('return')
            ->latest();
    }

    /**
     * Get all returns for this phone through issuances
     */
    public function returns()
    {
        return PhoneReturn::whereIn('phone_issuance_id',
            $this->issuances()->pluck('id')
        );
    }

    // Legacy support for backward compatibility with old phone_transactions table
    public function transactions()
    {
        return $this->hasMany(PhoneTransaction::class, 'serial_num', 'serial_num');
    }

    public function currentTransaction()
    {
        return $this->hasOne(PhoneTransaction::class, 'serial_num', 'serial_num')
        ->whereNull('date_returned')
        ->latestOfMany();
    }
    //  */
}
