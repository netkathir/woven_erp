<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialInwardItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_inward_id',
        'raw_material_id',
        'quantity_received',
        'unit_of_measure',
        'unit_price',
        'total_amount',
    ];

    protected $casts = [
        'quantity_received' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function materialInward()
    {
        return $this->belongsTo(MaterialInward::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}


