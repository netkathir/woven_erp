<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'transaction_type',
        'item_type',
        'item_id',
        'quantity',
        'unit_of_measure',
        'source_document_type',
        'source_document_id',
        'source_document_number',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'decimal:2',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }

    public function materialInward()
    {
        return $this->belongsTo(MaterialInward::class, 'source_document_id');
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'source_document_id');
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

    public function getItemNameAttribute()
    {
        if ($this->item_type === 'raw_material') {
            return $this->rawMaterial ? $this->rawMaterial->raw_material_name : '-';
        } elseif ($this->item_type === 'product') {
            return $this->product ? $this->product->product_name : '-';
        }
        return '-';
    }
}

