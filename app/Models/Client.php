<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'naziv_firme', 'adresa', 'postanski_broj_mjesto_drzava', 'pdv_broj', 'pib_number', 'email', 'invoice_email', 'kontakt_telefon', 'user_id',
    ];

    public function getInvoiceRecipientEmailAttribute(): ?string
    {
        return $this->invoice_email ?: $this->email;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'klijent_id');
    }
}
