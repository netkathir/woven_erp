@extends('layouts.dashboard')

@section('title', 'View Work Order - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Work Order Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('work-orders.edit', $workOrder->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('work-orders.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">Work Order Number</div>
            <div style="font-weight: 600; color: #111827;">{{ $workOrder->work_order_number }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Work Order Date</div>
            <div style="font-weight: 600; color: #111827;">{{ optional($workOrder->work_order_date)->format('d-m-Y') }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Customer</div>
            <div style="font-weight: 600; color: #111827;">{{ $workOrder->customer->customer_name ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Product</div>
            <div style="font-weight: 600; color: #111827;">{{ $workOrder->product->product_name ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Quantity to be Produced</div>
            <div style="font-weight: 600; color: #111827;">{{ number_format($workOrder->quantity_to_produce, 3) }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Work Order Type</div>
            <div style="font-weight: 600; color: #111827;">
                {{ $workOrder->work_order_type === 'customer_order' ? 'Customer Order' : 'Internal' }}
            </div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Status</div>
            <div style="font-weight: 600; color: #111827;">
                @if($workOrder->status === \App\Models\WorkOrder::STATUS_COMPLETED)
                    Completed
                @else
                    Open
                @endif
            </div>
        </div>
    </div>

    <h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Material Required & Consumption</h3>

    <div style="overflow-x: auto; margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 10px; text-align: left;">Material</th>
                    <th style="padding: 10px; text-align: left;">UOM</th>
                    <th style="padding: 10px; text-align: right;">Material Required</th>
                    <th style="padding: 10px; text-align: right;">Consumption</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workOrder->materials as $material)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px; color: #111827;">
                            {{ $material->rawMaterial->raw_material_name ?? '-' }}
                        </td>
                        <td style="padding: 8px; color: #111827;">
                            {{ $material->unit_of_measure }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($material->material_required, 3) }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($material->consumption, 3) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection


