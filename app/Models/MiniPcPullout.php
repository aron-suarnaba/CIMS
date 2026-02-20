<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniPcPullout extends Model
{
    use HasFactory;

    protected $table = 'mini_pc_pullouts';

    protected $fillable = [
        'mini_pc_issuance_id',
        'pullout_date',
        'reason',
    ];

    protected $casts = [
        'pullout_date' => 'date',
    ];

    public function issuance()
    {
        return $this->belongsTo(MiniPcIssuance::class, 'mini_pc_issuance_id');
    }
}
