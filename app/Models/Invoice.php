<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'company_id',
        'residential_company_id',
        'residential_company_name',
        'residential_company_address',
        'residential_company_city',
        'residential_company_postal_code',
        'residential_company_ico',
        'residential_company_dic',
        'residential_company_ic_dph',
        'residential_company_iban',
        'residential_company_bank_connection',
        'issue_date',
        'due_date',
        'status',
        'billing_month',
        'invoice_type',
        'desc_services',
    ];

    // Vzťah k spoločnosti (firma)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Vzťah k bytovému podniku (odberateľ)
    public function residentialCompany()
    {
        return $this->belongsTo(ResidentialCompany::class);
    }

    // Vzťah k službám cez pivot tabuľku invoice_service
    public function services()
    {
        return $this->hasMany(InvoiceService::class);
    }
    
}
