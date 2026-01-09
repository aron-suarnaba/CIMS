<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Computers;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComputerTransaction extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     *
     */

    protected $table = 'computer_transactions';

    protected $fillable = [
        'host_name',
        'assigned_user',
        'department',
        'date_issued',
        'pullout_date',
        'pullout_reason',
        'returned_to',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_issued' => 'date',
        'pullout_date' => 'date',
    ];

    /**
     * Get the computer that owns the transaction.
     * * We specify 'host_name' as the foreign key and 'host_name'
     * as the owner key to match your specific database setup.
     */
    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computers::class, 'host_name', 'host_name');
    }
}
