<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyExpenseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_expense_id',
        'item_description',
        'quantity',
        'unit_price',
        'total_amount',
        'gst_percentage',
        'gst_amount',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function dailyExpense()
    {
        return $this->belongsTo(DailyExpense::class);
    }
}

