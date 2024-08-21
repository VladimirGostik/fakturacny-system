<?php

// app/Models/Service.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'service_description', 'service_price'];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
