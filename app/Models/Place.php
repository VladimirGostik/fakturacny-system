<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = ['name','residential_company_id', 'header','desc_above_service','residential_company_name',
        'residential_company_address',
        'residential_company_city',
        'residential_company_postal_code',
        'residential_company_ico',
        'residential_company_dic',
        'residential_company_ic_dph',
        'residential_company_iban',
        'residential_company_bank_connection',
        'invoice_type',
        'desc_services'];

    public function residentialCompany()
    {
        return $this->belongsTo(ResidentialCompany::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
    
}
