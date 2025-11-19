<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'naziv_firme', 'adresa', 'postanski_broj_mjesto_drzava', 'pdv_broj', 'email', 'kontakt_telefon', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'klijent_id');
    }
}
