<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'address',
        'postal_code_city_country',
        'tax_number',
        'email',
        'phone',
        'bank_name',
        'account_number',
        'iban',
        'swift',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
