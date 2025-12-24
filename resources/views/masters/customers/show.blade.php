@extends('layouts.dashboard')

@section('title', 'Customer Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Customer Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('customers.edit', $customer->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('customers.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Customer ID</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 500;">{{ $customer->code }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Customer/Company Name</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->customer_name }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Contact Name</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->contact_name ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Phone Number</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->phone_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Email</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->email ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">GST Number</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->gst_number ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Billing Address</h3>
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Full Billing Address</label>
            <p style="color: #333; font-size: 16px; margin: 0; line-height: 1.6;">
                @if($customer->billing_address_line_1)
                    {{ $customer->billing_address_line_1 }}<br>
                    @if($customer->billing_address_line_2)
                        {{ $customer->billing_address_line_2 }}<br>
                    @endif
                    @if($customer->billing_city || $customer->billing_state || $customer->billing_postal_code)
                        {{ $customer->billing_city }}{{ $customer->billing_city && $customer->billing_state ? ', ' : '' }}{{ $customer->billing_state }}{{ ($customer->billing_city || $customer->billing_state) && $customer->billing_postal_code ? ' - ' : '' }}{{ $customer->billing_postal_code }}<br>
                    @endif
                    @if($customer->billing_country)
                        {{ $customer->billing_country }}
                    @endif
                @else
                    N/A
                @endif
            </p>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Shipping Address</h3>
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Full Shipping Address</label>
            <p style="color: #333; font-size: 16px; margin: 0; line-height: 1.6;">
                @if($customer->shipping_address_line_1)
                    {{ $customer->shipping_address_line_1 }}<br>
                    @if($customer->shipping_address_line_2)
                        {{ $customer->shipping_address_line_2 }}<br>
                    @endif
                    @if($customer->shipping_city || $customer->shipping_state || $customer->shipping_postal_code)
                        {{ $customer->shipping_city }}{{ $customer->shipping_city && $customer->shipping_state ? ', ' : '' }}{{ $customer->shipping_state }}{{ ($customer->shipping_city || $customer->shipping_state) && $customer->shipping_postal_code ? ' - ' : '' }}{{ $customer->shipping_postal_code }}<br>
                    @endif
                    @if($customer->shipping_country)
                        {{ $customer->shipping_country }}
                    @endif
                @else
                    N/A
                @endif
            </p>
        </div>
    </div>


    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Additional Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created At</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $customer->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Last Updated</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $customer->updated_at->format('d M Y, h:i A') }}</p>
            </div>
            @if($customer->creator)
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created By</label>
                    <p style="color: #333; font-size: 16px; margin: 0;">{{ $customer->creator->name }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

