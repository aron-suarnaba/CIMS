<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Credential extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password',
        'url',
        'notes',
    ];

    /**
     * Encrypt the password when setting it.
     */
    public function setPasswordAttribute($value)
    {
        if ($value === null) {
            $this->attributes['password'] = null;
            return;
        }
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt the password when retrieving it.
     */
    public function getPasswordAttribute($value)
    {
        if ($value === null) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            // in case decryption fails just return raw value
            return $value;
        }
    }
}
