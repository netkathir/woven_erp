@extends('layouts.dashboard')

@section('title', 'Raw Material Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Raw Material Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('raw-materials.edit', $rawMaterial->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('raw-materials.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Raw Material Code</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 500;">{{ $rawMaterial->code }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Raw Material Name</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $rawMaterial->raw_material_name }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Unit of Measure</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $rawMaterial->unit_of_measure }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Status</label>
                <p style="margin: 0 0 20px 0;">
                    @if($rawMaterial->is_active)
                        <span style="padding: 4px 12px; background: #d4edda; color: #155724; border-radius: 12px; font-size: 12px;">Active</span>
                    @else
                        <span style="padding: 4px 12px; background: #f8d7da; color: #721c24; border-radius: 12px; font-size: 12px;">Inactive</span>
                    @endif
                </p>
            </div>
            @if($rawMaterial->description)
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Description</label>
                    <p style="color: #333; font-size: 16px; margin: 0;">{{ $rawMaterial->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Stock Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Quantity Available</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; {{ $rawMaterial->isLowStock() ? 'color: #dc3545; font-weight: 600;' : '' }}">
                    {{ number_format($rawMaterial->quantity_available, 2) }} {{ $rawMaterial->unit_of_measure }}
                    @if($rawMaterial->isLowStock())
                        <span style="padding: 4px 8px; background: #fff3cd; color: #856404; border-radius: 4px; font-size: 12px; margin-left: 10px;">Low Stock</span>
                    @endif
                </p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Reorder Level</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ number_format($rawMaterial->reorder_level, 2) }} {{ $rawMaterial->unit_of_measure }}</p>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Supplier & Pricing Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Supplier</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">
                    @if($rawMaterial->supplier)
                        {{ $rawMaterial->supplier->supplier_name }} ({{ $rawMaterial->supplier->code }})
                    @else
                        N/A
                    @endif
                </p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Price per Unit</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">₹{{ number_format($rawMaterial->price_per_unit, 2) }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">GST Percentage</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ number_format($rawMaterial->gst_percentage, 2) }}%</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Total Value</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 600;">
                    ₹{{ number_format($rawMaterial->quantity_available * $rawMaterial->price_per_unit, 2) }}
                </p>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Additional Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created At</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $rawMaterial->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Last Updated</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $rawMaterial->updated_at->format('d M Y, h:i A') }}</p>
            </div>
            @if($rawMaterial->creator)
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created By</label>
                    <p style="color: #333; font-size: 16px; margin: 0;">{{ $rawMaterial->creator->name }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

