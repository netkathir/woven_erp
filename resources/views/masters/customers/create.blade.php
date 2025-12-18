@extends('layouts.dashboard')

@section('title', 'Create Customer - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Create New Customer</h2>
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

    <form action="{{ route('customers.store') }}" method="POST" id="customerForm">
        @csrf

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="customer_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer Name <span style="color: red;">*</span></label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter customer name">
                @error('customer_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="contact_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Name</label>
                <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter primary contact person name">
                @error('contact_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label for="phone_number" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter phone number">
                    @error('phone_number')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
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
                <input type="text" name="billing_address_line_1" id="billing_address_line_1" value="{{ old('billing_address_line_1') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter billing address line 1">
                @error('billing_address_line_1')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 15px;">
                <label for="billing_address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
                <input type="text" name="billing_address_line_2" id="billing_address_line_2" value="{{ old('billing_address_line_2') }}"
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
                    <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city') }}"
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
                            <option value="{{ $state }}" {{ old('billing_state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('billing_state')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_postal_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Postal Code</label>
                    <input type="text" name="billing_postal_code" id="billing_postal_code" value="{{ old('billing_postal_code') }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter postal code">
                    @error('billing_postal_code')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="billing_country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
                <input type="text" name="billing_country" id="billing_country" value="{{ old('billing_country', 'India') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter country">
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
                <input type="text" name="shipping_address_line_1" id="shipping_address_line_1" value="{{ old('shipping_address_line_1') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter shipping address line 1">
                @error('shipping_address_line_1')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 15px;">
                <label for="shipping_address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
                <input type="text" name="shipping_address_line_2" id="shipping_address_line_2" value="{{ old('shipping_address_line_2') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter shipping address line 2">
                @error('shipping_address_line_2')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label for="shipping_city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City</label>
                    <input type="text" name="shipping_city" id="shipping_city" value="{{ old('shipping_city') }}"
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
                            <option value="{{ $state }}" {{ old('shipping_state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('shipping_state')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="shipping_postal_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Postal Code</label>
                    <input type="text" name="shipping_postal_code" id="shipping_postal_code" value="{{ old('shipping_postal_code') }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter postal code">
                    @error('shipping_postal_code')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="shipping_country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
                <input type="text" name="shipping_country" id="shipping_country" value="{{ old('shipping_country', 'India') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter country">
                @error('shipping_country')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Business Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label for="payment_terms" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Payment Terms <span style="color: red;">*</span></label>
                    <select name="payment_terms" id="payment_terms" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select payment terms</option>
                        <option value="cash" {{ old('payment_terms') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="credit" {{ old('payment_terms') === 'credit' || old('payment_terms') === '' ? 'selected' : '' }}>Credit</option>
                        <option value="advance" {{ old('payment_terms') === 'advance' ? 'selected' : '' }}>Advance</option>
                        <option value="partial" {{ old('payment_terms') === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="other" {{ old('payment_terms') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('payment_terms')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="credit_limit" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Credit Limit</label>
                    <input type="number" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', 0) }}" step="0.01" min="0"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="0.00">
                    @error('credit_limit')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="gst_number" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST Number</label>
                <input type="text" name="gst_number" id="gst_number" value="{{ old('gst_number') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter GST number (if applicable)">
                @error('gst_number')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="notes" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;"
                    placeholder="Enter any additional notes (optional)">{{ old('notes') }}</textarea>
                @error('notes')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="color: #333; font-weight: 500;">Active</span>
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="clearForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Clear
            </button>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Submit
            </button>
            <a href="{{ route('customers.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function clearForm() {
        document.getElementById('customerForm').reset();
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
        } else {
            document.getElementById('shipping_address_line_1').value = '';
            document.getElementById('shipping_address_line_2').value = '';
            document.getElementById('shipping_city').value = '';
            document.getElementById('shipping_state').value = '';
            document.getElementById('shipping_postal_code').value = '';
            document.getElementById('shipping_country').value = '';
        }
    }
</script>
@endpush
@endsection

