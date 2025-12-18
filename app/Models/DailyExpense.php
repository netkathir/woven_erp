<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_id',
        'expense_date',
        'expense_category',
        'vendor_name',
        'payment_method',
        'invoice_number',
        'amount',
        'gst_applied',
        'total_expense_amount',
        'total_gst_amount',
        'grand_total',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'gst_applied' => 'decimal:2',
        'total_expense_amount' => 'decimal:2',
        'total_gst_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(DailyExpenseItem::class);
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

