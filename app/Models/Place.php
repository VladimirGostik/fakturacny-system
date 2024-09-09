<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = ['name','residential_company_id', 'header','desc_above_service'];

    public function residentialCompany()
    {
        return $this->belongsTo(ResidentialCompany::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
    
}
