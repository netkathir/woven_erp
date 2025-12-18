@extends('layouts.dashboard')

@section('title', 'View Stock Transaction - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Stock Transaction Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('stock-transactions.edit', $stockTransaction->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('stock-transactions.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">Transaction Number</div>
            <div style="font-weight: 600; color: #111827;">{{ $stockTransaction->transaction_number }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Transaction Date</div>
            <div style="font-weight: 600; color: #111827;">{{ optional($stockTransaction->transaction_date)->format('d-m-Y') }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Transaction Type</div>
            <div style="font-weight: 600; color: #111827;">
                @if($stockTransaction->transaction_type === 'stock_in')
                    <span style="padding: 4px 8px; background: #28a745; color: white; border-radius: 4px; font-size: 12px;">Stock In</span>
                @else
                    <span style="padding: 4px 8px; background: #dc3545; color: white; border-radius: 4px; font-size: 12px;">Stock Out</span>
                @endif
            </div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Item Type</div>
            <div style="font-weight: 600; color: #111827;">{{ ucfirst(str_replace('_', ' ', $stockTransaction->item_type)) }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Item Name</div>
            <div style="font-weight: 600; color: #111827;">{{ $stockTransaction->item_name }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Quantity</div>
            <div style="font-weight: 600; color: #111827;">{{ number_format($stockTransaction->quantity, 3) }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Unit of Measure</div>
            <div style="font-weight: 600; color: #111827;">{{ $stockTransaction->unit_of_measure }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Source Document Type</div>
            <div style="font-weight: 600; color: #111827;">
                @if($stockTransaction->source_document_type)
                    {{ ucfirst(str_replace('_', ' ', $stockTransaction->source_document_type)) }}
                @else
                    -
                @endif
            </div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Source Document Number</div>
            <div style="font-weight: 600; color: #111827;">{{ $stockTransaction->source_document_number ?: '-' }}</div>
        </div>
    </div>
</div>
@endsection

