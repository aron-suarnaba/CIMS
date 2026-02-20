<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniPcIssuance extends Model
{
    use HasFactory;

    protected $table = 'mini_pc_issuances';

    protected $fillable = [
        'mini_pc_id',
        'department',
        'location',
        'date_issued',
    ];

    protected $casts = [
        'date_issued' => 'date',
    ];

    public function miniPc()
    {
        return $this->belongsTo(MiniPc::class, 'mini_pc_id');
    }

    public function pullout()
    {
        return $this->hasOne(MiniPcPullout::class, 'mini_pc_issuance_id');
    }
}
