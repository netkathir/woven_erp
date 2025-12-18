@extends('layouts.dashboard')

@section('title', 'View Daily Expense - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Daily Expense Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('daily-expenses.edit', $dailyExpense->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('daily-expenses.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">Expense ID</div>
            <div style="font-weight: 600; color: #111827;">{{ $dailyExpense->expense_id }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Date</div>
            <div style="font-weight: 600; color: #111827;">{{ optional($dailyExpense->expense_date)->format('d-m-Y') }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Expense Category</div>
            <div style="font-weight: 600; color: #111827;">{{ $dailyExpense->expense_category }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Vendor Name</div>
            <div style="font-weight: 600; color: #111827;">{{ $dailyExpense->vendor_name }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Payment Method</div>
            <div style="font-weight: 600; color: #111827;">
                @if($dailyExpense->payment_method === 'cash')
                    Cash
                @elseif($dailyExpense->payment_method === 'credit')
                    Credit
                @elseif($dailyExpense->payment_method === 'bank_transfer')
                    Bank Transfer
                @else
                    {{ $dailyExpense->payment_method }}
                @endif
            </div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Invoice Number</div>
            <div style="font-weight: 600; color: #111827;">{{ $dailyExpense->invoice_number ?: '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Amount</div>
            <div style="font-weight: 600; color: #111827;">{{ number_format($dailyExpense->amount, 2) }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">GST Applied</div>
            <div style="font-weight: 600; color: #111827;">{{ $dailyExpense->gst_applied !== null ? $dailyExpense->gst_applied . '%' : '-' }}</div>
        </div>
    </div>

    <h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Expense Items</h3>

    <div style="overflow-x: auto; margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 10px; text-align: left;">Item Description</th>
                    <th style="padding: 10px; text-align: right;">Quantity</th>
                    <th style="padding: 10px; text-align: right;">Unit Price</th>
                    <th style="padding: 10px; text-align: right;">Total Amount</th>
                    <th style="padding: 10px; text-align: right;">GST %</th>
                    <th style="padding: 10px; text-align: right;">GST Amount</th>
                    <th style="padding: 10px; text-align: right;">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyExpense->items as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px; color: #111827;">
                            {{ $item->item_description }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->quantity, 3) }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->total_amount, 2) }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ $item->gst_percentage !== null ? number_format($item->gst_percentage, 2) . '%' : '-' }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->gst_amount, 2) }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->line_total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
        <div style="max-width: 360px; width: 100%; background: #f9fafb; padding: 16px 18px; border-radius: 8px; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">Total Expense Amount:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($dailyExpense->total_expense_amount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">GST Applied:</span>
                <span style="font-weight: 600; color: #111827;">{{ $dailyExpense->gst_applied !== null ? $dailyExpense->gst_applied . '%' : '-' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">Total GST Amount:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($dailyExpense->total_gst_amount, 2) }}</span>
            </div>
            <div style="height: 1px; background: #e5e7eb; margin: 8px 0 10px;"></div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-weight: 700; color: #111827;">Grand Total:</span>
                <span style="font-weight: 700; color: #111827;">{{ number_format($dailyExpense->grand_total, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

