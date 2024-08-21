<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentialCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'postal_code',
        'city',
        'ico',
        'dic',
        'ic_dph',
        'iban',
        'bank_connection',
        'company_id',
    ];

    public function places()
    {
        return $this->hasMany(Place::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
