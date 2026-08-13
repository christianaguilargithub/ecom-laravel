<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    // use HasFactory;
    protected $fillable = [
        'user_id','label','recipient_name','phone',
        'address_line','city','province','postal_code','country',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



}
