<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'product_id',
        'item_description',
        'quantity',
        'uom',
        'price',
        'amount',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the quotation that owns the item.
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Get the product for the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

