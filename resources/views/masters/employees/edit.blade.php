@extends('layouts.dashboard')

@section('title', 'Edit Employee - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Employee</h2>
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

    <form action="{{ route('employees.update', $employee->id) }}" method="POST" id="employeeForm">
        @csrf
        @method('PUT')

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Employee Code</label>
                <input type="text" value="{{ $employee->code }}" disabled
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="employee_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Employee Name <span style="color: red;">*</span></label>
                <input type="text" name="employee_name" id="employee_name" value="{{ old('employee_name', $employee->employee_name) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter employee name">
                @error('employee_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label for="designation" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Designation</label>
                    <input type="text" name="designation" id="designation" value="{{ old('designation', $employee->designation) }}"
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
                            <option value="{{ $key }}" {{ old('department', $employee->department) === $key ? 'selected' : '' }}>{{ $label }}</option>
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
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $employee->phone_number) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter phone number">
                    @error('phone_number')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}"
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
                <input type="text" name="address" id="address" value="{{ old('address', $employee->address) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter address">
                @error('address')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label for="salary" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Salary</label>
                    <input type="number" name="salary" id="salary" value="{{ old('salary', $employee->salary) }}" step="0.01" min="0"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="0.00">
                    @error('salary')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="joining_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Joining Date</label>
                    <input type="date" name="joining_date" id="joining_date" value="{{ old('joining_date', optional($employee->joining_date)->format('Y-m-d')) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('joining_date')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="manager_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Manager</label>
                <select name="manager_id" id="manager_id"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">Select manager</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ old('manager_id', $employee->manager_id) == $manager->id ? 'selected' : '' }}>
                            {{ $manager->employee_name }} ({{ $manager->designation ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('manager_id')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}
                        style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="color: #333; font-weight: 500;">Active</span>
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="resetForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Reset
            </button>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update
            </button>
            <a href="{{ route('employees.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function resetForm() {
        document.getElementById('employeeForm').reset();
        document.getElementById('employee_name').value = '{{ addslashes($employee->employee_name) }}';
        document.getElementById('designation').value = '{{ addslashes($employee->designation ?? '') }}';
        document.getElementById('department').value = '{{ $employee->department }}';
        document.getElementById('phone_number').value = '{{ addslashes($employee->phone_number ?? '') }}';
        document.getElementById('email').value = '{{ addslashes($employee->email ?? '') }}';
        document.getElementById('address').value = '{{ addslashes($employee->address ?? '') }}';
        document.getElementById('salary').value = '{{ $employee->salary }}';
        document.getElementById('joining_date').value = '{{ optional($employee->joining_date)->format('Y-m-d') }}';
        document.getElementById('manager_id').value = '{{ $employee->manager_id }}';
        document.querySelector('input[name="is_active"]').checked = {{ $employee->is_active ? 'true' : 'false' }};
    }
</script>
@endpush
@endsection


