<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // Definujeme masovo priraditeľné stĺpce
    protected $fillable = [
        'name',
        'address',
        'postal_code',
        'city',
        'ico',
        'dic',
        'iban',
        'bank_connection',  // Pridali sme Bankové spojenie
    ];

    public function residentialCompanies()
    {
        return $this->hasMany(ResidentialCompany::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
