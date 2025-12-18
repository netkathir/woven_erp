<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_name',
        'code',
        'unit_of_measure',
        'product_category',
        'price_per_unit',
        'stock_quantity',
        'gst_percentage',
        'description',
        'is_active',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the organization that owns the product.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the branch that owns the product.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the product.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
