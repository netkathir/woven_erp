@extends('layouts.dashboard')

@section('title', 'Create Employee - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Create New Employee</h2>
        <a href="{{ route('employees.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('employees.store') }}" method="POST" id="employeeForm">
        @csrf

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="employee_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Employee Name <span style="color: red;">*</span></label>
                <input type="text" name="employee_name" id="employee_name" value="{{ old('employee_name') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter employee name">
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Employee ID will be auto-generated (EMP001, EMP002, etc.)</small>
                @error('employee_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label for="designation" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Designation</label>
                    <input type="text" name="designation" id="designation" value="{{ old('designation') }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="e.g., Manager, Salesperson">
                    @error('designation')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="department" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Department</label>
                    <select name="department" id="department"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select department</option>
                        @foreach($departments as $key => $label)
                            <option value="{{ $key }}" {{ old('department') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('department')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
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
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Employment Details</h3>

            <div style="margin-bottom: 20px;">
                <label for="address" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter address">
                @error('address')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="joining_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Joining Date</label>
                <input type="date" name="joining_date" id="joining_date" value="{{ old('joining_date') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('joining_date')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="clearForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Clear
            </button>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Submit
            </button>
            <a href="{{ route('employees.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function clearForm() {
        document.getElementById('employeeForm').reset();
    }
</script>
@endpush
@endsection


