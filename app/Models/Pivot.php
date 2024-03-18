<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pivot extends Model
{
    use HasFactory;

    protected $table = 'pivot_table';
    protected $fillable = [
        'seller_name',
        'product_category', 
        'product_name', 
        'product_description',
        'product_price'
    ];
}
