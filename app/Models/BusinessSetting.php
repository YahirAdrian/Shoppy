<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'business_name',
        'logo',
        'address',
        'phone',
        'email',
        'currency_symbol',
        'low_stock',
        'receipt_header',
        'receipt_footer',
    ];
}
