@extends('layouts.dashboard')

@section('title', 'Edit Customer - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Customer</h2>
        <a href="{{ route('customers.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customers.update', $customer->id) }}" method="POST" id="customerForm">
        @csrf
        @method('PUT')

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #666; font-weight: 500;">Customer ID</label>
                <div style="padding: 12px; background: #e9ecef; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; font-weight: 500;">
                    {{ $customer->code }}
                </div>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Customer ID cannot be changed</small>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="customer_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer/Company Name <span style="color: red;">*</span></label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $customer->customer_name) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter customer or company name">
                @error('customer_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="contact_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Name</label>
                <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', $customer->contact_name) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter primary contact person name">
                @error('contact_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label for="phone_number" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $customer->phone_number) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter phone number">
                    @error('phone_number')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter email address">
                    @error('email')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Billing Address</h3>
            
            <div style="margin-bottom: 15px;">
                <label for="billing_address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1</label>
                <input type="text" name="billing_address_line_1" id="billing_address_line_1" value="{{ old('billing_address_line_1', $customer->billing_address_line_1) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter billing address line 1">
                @error('billing_address_line_1')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 15px;">
                <label for="billing_address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
                <input type="text" name="billing_address_line_2" id="billing_address_line_2" value="{{ old('billing_address_line_2', $customer->billing_address_line_2) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter billing address line 2">
                @error('billing_address_line_2')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            @php
                $states = [
                    'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana',
                    'Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur',
                    'Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu',
                    'Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
                    'Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu',
                    'Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'
                ];
            @endphp

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label for="billing_city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City</label>
                    <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city', $customer->billing_city) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter city">
                    @error('billing_city')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State</label>
                    <select name="billing_state" id="billing_state"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select state</option>
                        @foreach($states as $state)
                            <option value="{{ $state }}" {{ old('billing_state', $customer->billing_state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('billing_state')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_postal_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Postal Code</label>
                    <input type="text" name="billing_postal_code" id="billing_postal_code" value="{{ old('billing_postal_code', $customer->billing_postal_code) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter postal code">
                    @error('billing_postal_code')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="billing_country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
                <select name="billing_country" id="billing_country"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">Select country</option>
                    @php
                        $countries = [
                            'India', 'United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France',
                            'Japan', 'China', 'Brazil', 'Russia', 'South Korea', 'Italy', 'Spain', 'Mexico',
                            'Indonesia', 'Netherlands', 'Saudi Arabia', 'Turkey', 'Switzerland', 'Singapore',
                            'Malaysia', 'Thailand', 'Philippines', 'Vietnam', 'Bangladesh', 'Pakistan', 'Sri Lanka',
                            'Nepal', 'Myanmar', 'Other'
                        ];
                    @endphp
                    @foreach($countries as $country)
                        <option value="{{ $country }}" {{ old('billing_country', $customer->billing_country) === $country ? 'selected' : '' }}>{{ $country }}</option>
                    @endforeach
                </select>
                @error('billing_country')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Shipping Address</h3>
            <div style="margin-bottom: 15px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" id="same_as_billing" onclick="copyBillingToShipping()"
                        style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="color: #333; font-weight: 500;">Same as Billing Address</span>
                </label>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="shipping_address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1</label>
                <input type="text" name="shipping_address_line_1" id="shipping_address_line_1" value="{{ old('shipping_address_line_1', $customer->shipping_address_line_1) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter shipping address line 1">
                @error('shipping_address_line_1')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 15px;">
                <label for="shipping_address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
                <input type="text" name="shipping_address_line_2" id="shipping_address_line_2" value="{{ old('shipping_address_line_2', $customer->shipping_address_line_2) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter shipping address line 2">
                @error('shipping_address_line_2')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label for="shipping_city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City</label>
                    <input type="text" name="shipping_city" id="shipping_city" value="{{ old('shipping_city', $customer->shipping_city) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter city">
                    @error('shipping_city')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="shipping_state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State</label>
                    <select name="shipping_state" id="shipping_state"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select state</option>
                        @foreach($states as $state)
                            <option value="{{ $state }}" {{ old('shipping_state', $customer->shipping_state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('shipping_state')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="shipping_postal_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Postal Code</label>
                    <input type="text" name="shipping_postal_code" id="shipping_postal_code" value="{{ old('shipping_postal_code', $customer->shipping_postal_code) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter postal code">
                    @error('shipping_postal_code')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="shipping_country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
                <select name="shipping_country" id="shipping_country"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">Select country</option>
                    @php
                        $countries = [
                            'India', 'United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France',
                            'Japan', 'China', 'Brazil', 'Russia', 'South Korea', 'Italy', 'Spain', 'Mexico',
                            'Indonesia', 'Netherlands', 'Saudi Arabia', 'Turkey', 'Switzerland', 'Singapore',
                            'Malaysia', 'Thailand', 'Philippines', 'Vietnam', 'Bangladesh', 'Pakistan', 'Sri Lanka',
                            'Nepal', 'Myanmar', 'Other'
                        ];
                    @endphp
                    @foreach($countries as $country)
                        <option value="{{ $country }}" {{ old('shipping_country', $customer->shipping_country) === $country ? 'selected' : '' }}>{{ $country }}</option>
                    @endforeach
                </select>
                @error('shipping_country')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Business Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="gst_number" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST Number</label>
                <input type="text" name="gst_number" id="gst_number" value="{{ old('gst_number', $customer->gst_number) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter GST number (if applicable)">
                @error('gst_number')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="resetForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Reset
            </button>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update
            </button>
            <a href="{{ route('customers.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function checkIfAddressesMatch() {
        const billingLine1 = document.getElementById('billing_address_line_1').value || '';
        const billingLine2 = document.getElementById('billing_address_line_2').value || '';
        const billingCity = document.getElementById('billing_city').value || '';
        const billingState = document.getElementById('billing_state').value || '';
        const billingPostal = document.getElementById('billing_postal_code').value || '';
        const billingCountry = document.getElementById('billing_country').value || '';
        
        const shippingLine1 = document.getElementById('shipping_address_line_1').value || '';
        const shippingLine2 = document.getElementById('shipping_address_line_2').value || '';
        const shippingCity = document.getElementById('shipping_city').value || '';
        const shippingState = document.getElementById('shipping_state').value || '';
        const shippingPostal = document.getElementById('shipping_postal_code').value || '';
        const shippingCountry = document.getElementById('shipping_country').value || '';
        
        const checkbox = document.getElementById('same_as_billing');
        
        if (!checkbox) return;
        
        // Check if all address fields match
        if (billingLine1 === shippingLine1 &&
            billingLine2 === shippingLine2 &&
            billingCity === shippingCity &&
            billingState === shippingState &&
            billingPostal === shippingPostal &&
            billingCountry === shippingCountry) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    }

    function resetForm() {
        document.getElementById('customerForm').reset();
        // Restore original values
        document.getElementById('customer_name').value = '{{ addslashes($customer->customer_name) }}';
        document.getElementById('contact_name').value = '{{ addslashes($customer->contact_name ?? '') }}';
        document.getElementById('phone_number').value = '{{ addslashes($customer->phone_number ?? '') }}';
        document.getElementById('email').value = '{{ addslashes($customer->email ?? '') }}';
        document.getElementById('billing_address_line_1').value = '{{ addslashes($customer->billing_address_line_1 ?? '') }}';
        document.getElementById('billing_address_line_2').value = '{{ addslashes($customer->billing_address_line_2 ?? '') }}';
        document.getElementById('billing_city').value = '{{ addslashes($customer->billing_city ?? '') }}';
        document.getElementById('billing_state').value = '{{ addslashes($customer->billing_state ?? '') }}';
        document.getElementById('billing_postal_code').value = '{{ addslashes($customer->billing_postal_code ?? '') }}';
        document.getElementById('billing_country').value = '{{ addslashes($customer->billing_country ?? '') }}';
        document.getElementById('shipping_address_line_1').value = '{{ addslashes($customer->shipping_address_line_1 ?? '') }}';
        document.getElementById('shipping_address_line_2').value = '{{ addslashes($customer->shipping_address_line_2 ?? '') }}';
        document.getElementById('shipping_city').value = '{{ addslashes($customer->shipping_city ?? '') }}';
        document.getElementById('shipping_state').value = '{{ addslashes($customer->shipping_state ?? '') }}';
        document.getElementById('shipping_postal_code').value = '{{ addslashes($customer->shipping_postal_code ?? '') }}';
        document.getElementById('shipping_country').value = '{{ addslashes($customer->shipping_country ?? '') }}';
        document.getElementById('gst_number').value = '{{ addslashes($customer->gst_number ?? '') }}';
        
        // Re-check if addresses match after reset
        setTimeout(checkIfAddressesMatch, 100);
    }

    function copyBillingToShipping() {
        const checkbox = document.getElementById('same_as_billing');
        if (checkbox.checked) {
            document.getElementById('shipping_address_line_1').value = document.getElementById('billing_address_line_1').value;
            document.getElementById('shipping_address_line_2').value = document.getElementById('billing_address_line_2').value;
            document.getElementById('shipping_city').value = document.getElementById('billing_city').value;
            document.getElementById('shipping_state').value = document.getElementById('billing_state').value;
            document.getElementById('shipping_postal_code').value = document.getElementById('billing_postal_code').value;
            document.getElementById('shipping_country').value = document.getElementById('billing_country').value;
        }
    }
    
    // Also copy when billing fields change if checkbox is checked
    document.addEventListener('DOMContentLoaded', function() {
        const billingFields = ['billing_address_line_1', 'billing_address_line_2', 'billing_city', 'billing_state', 'billing_postal_code', 'billing_country'];
        billingFields.forEach(function(fieldId) {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('change', function() {
                    if (document.getElementById('same_as_billing').checked) {
                        const shippingFieldId = fieldId.replace('billing_', 'shipping_');
                        document.getElementById(shippingFieldId).value = field.value;
                    }
                });
            }
        });
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Check if addresses match on initial load
        setTimeout(checkIfAddressesMatch, 100);
        
        // Monitor billing and shipping address changes to update checkbox state
        const billingFields = ['billing_address_line_1', 'billing_address_line_2', 'billing_city', 'billing_state', 'billing_postal_code', 'billing_country'];
        const shippingFields = ['shipping_address_line_1', 'shipping_address_line_2', 'shipping_city', 'shipping_state', 'shipping_postal_code', 'shipping_country'];
        
        billingFields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.addEventListener('change', checkIfAddressesMatch);
                element.addEventListener('input', checkIfAddressesMatch);
            }
        });
        
        shippingFields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.addEventListener('change', checkIfAddressesMatch);
                element.addEventListener('input', checkIfAddressesMatch);
            }
        });
    });
</script>
@endpush
@endsection

