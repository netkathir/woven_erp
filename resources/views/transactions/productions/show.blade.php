@extends('layouts.dashboard')

@section('title', 'View Production - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Production Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('productions.edit', $production->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('productions.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">Work Order Number</div>
            <div style="font-weight: 600; color: #111827;">{{ $production->workOrder->work_order_number ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Product Name</div>
            <div style="font-weight: 600; color: #111827;">{{ $production->product->product_name ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Produced Quantity</div>
            <div style="font-weight: 600; color: #111827;">{{ number_format($production->produced_quantity, 3) }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Weight of 1 Bag/Unit</div>
            <div style="font-weight: 600; color: #111827;">{{ number_format($production->weight_per_unit, 3) }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Total Weight Produced</div>
            <div style="font-weight: 600; color: #111827;">{{ number_format($production->total_weight, 3) }}</div>
        </div>
    </div>

    <div style="max-width: 640px;">
        <div style="font-size: 13px; color: #6b7280; margin-bottom: 4px;">Remarks</div>
        <div style="padding: 10px; border-radius: 5px; border: 1px solid #e5e7eb; background: #f9fafb; min-height: 60px; white-space: pre-wrap;">
            {{ $production->remarks ?: '-' }}
        </div>
    </div>
</div>
@endsection


