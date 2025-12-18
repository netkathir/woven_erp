<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'raw_material_name',
        'code',
        'unit_of_measure',
        'quantity_available',
        'reorder_level',
        'supplier_id',
        'price_per_unit',
        'gst_percentage',
        'description',
        'is_active',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'quantity_available' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the supplier that supplies this raw material.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the organization that owns the raw material.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the branch that owns the raw material.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the raw material.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if stock is below reorder level.
     */
    public function isLowStock(): bool
    {
        return $this->quantity_available <= $this->reorder_level;
    }
}
