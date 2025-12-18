<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'work_order_id',
        'product_id',
        'produced_quantity',
        'weight_per_unit',
        'total_weight',
        'remarks',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'produced_quantity' => 'decimal:3',
        'weight_per_unit' => 'decimal:3',
        'total_weight' => 'decimal:3',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}


