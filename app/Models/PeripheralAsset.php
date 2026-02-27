<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class PeripheralAsset extends Model
{
    use HasFactory;

    protected $table = 'peripheral_assets';

    protected $fillable = [
        'brand',
        'image_path',
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

    protected $appends = [
        'image_url',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return URL::to('/' . ltrim($this->image_path, '/'));
    }

    public function issuances()
    {
        return $this->hasMany(PeripheralIssuance::class, 'serial_num', 'serial_num');
    }

    public function currentIssuance()
    {
        return $this->hasOne(PeripheralIssuance::class, 'serial_num', 'serial_num')
            ->whereDoesntHave('return')
            ->latest();
    }
}
