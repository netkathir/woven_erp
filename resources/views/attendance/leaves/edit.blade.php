@extends('layouts.dashboard')

@section('title', 'Edit Leave Request - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Edit Leave Request</h2>
        <a href="{{ route('leaves.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>There were some problems with your input:</strong>
            <ul style="margin-top: 8px; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('leaves.update', $leaf) }}">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px;">
            <div>
                <label for="employee_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Employee Name <span style="color:red">*</span></label>
                <select name="employee_id" id="employee_id" required
                        style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="">-- Select Employee --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id', $leaf->employee_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->employee_name }} ({{ $employee->code }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="leave_type_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Leave Type <span style="color:red">*</span></label>
                <select name="leave_type_id" id="leave_type_id" required
                        style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="">-- Select Leave Type --</option>
                    @foreach($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}" {{ old('leave_type_id', $leaf->leave_type_id) == $leaveType->id ? 'selected' : '' }}>
                            {{ $leaveType->name }}
                        </option>
                    @endforeach
                </select>
                @error('leave_type_id')
                    <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Date for Absentee <span style="color:red">*</span></label>
                <input type="date" name="date" id="date" required value="{{ old('date', optional($leaf->date)->format('Y-m-d')) }}"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                @error('date')
                    <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 2px solid #dee2e6;">
                <input type="checkbox" name="change_to_present" id="change_to_present" value="1"
                       {{ old('change_to_present', $leaf->change_to_present) ? 'checked' : '' }}
                       style="width: 20px; height: 20px; cursor: pointer;">
                <div>
                    <div style="font-weight: 600; color: #333; margin-bottom: 4px;">Leave Status Change (Absentee to Present)</div>
                    <div style="font-size: 13px; color: #666;">
                        Check this if the employee later appears in production and should be marked as Present instead of Absent.
                    </div>
                </div>
            </label>
        </div>

        <div style="margin-bottom: 25px;">
            <label for="remarks" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Remarks</label>
            <textarea name="remarks" id="remarks" rows="3"
                      placeholder="Any remarks related to the status change (optional)"
                      style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-family: inherit;">{{ old('remarks', $leaf->remarks) }}</textarea>
            @error('remarks')
                <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <a href="{{ route('leaves.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                Cancel
            </a>
            <button type="submit" style="padding: 10px 22px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Update Leave Request
            </button>
        </div>
    </form>
</div>
@endsection

