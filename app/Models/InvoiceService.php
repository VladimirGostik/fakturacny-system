<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceService extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'service_description',
        'service_price',
        'place_name',
        'place_header',
    ];

    // Vzťah k faktúre
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
