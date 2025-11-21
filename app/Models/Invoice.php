<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'broj_fakture', 'datum_izdavanja', 'klijent_id', 'opis_posla',
        'kolicina', 'cijena', 'valuta', 'placeno', 'datum_placanja',
        'uplaceni_iznos_eur', 'user_id', 'name', 'email',
    ];

    protected $casts = [
        'datum_izdavanja' => 'datetime',
        'datum_placanja' => 'datetime',
        'placeno' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'klijent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBamAmountAttribute()
    {
        return $this->valuta === 'EUR' ? $this->cijena * 1.95583 : $this->cijena;
    }

    public function getPaidBamAmountAttribute()
    {
        return $this->valuta === 'EUR' && $this->uplaceni_iznos_eur
            ? $this->uplaceni_iznos_eur * 1.95583
            : $this->cijena;
    }
}
