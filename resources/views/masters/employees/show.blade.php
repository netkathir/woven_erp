@extends('layouts.dashboard')

@section('title', 'Employee Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Employee Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('employees.edit', $employee->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('employees.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Basic Information --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Employee Code</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 500;">{{ $employee->code }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Employee Name</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $employee->employee_name }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Designation</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $employee->designation ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Department</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $employee->department ? ucfirst($employee->department) : 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Phone Number</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $employee->phone_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Email</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $employee->email ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Status</label>
                <p style="margin: 0 0 20px 0;">
                    @if($employee->is_active)
                        <span style="padding: 4px 12px; background: #d4edda; color: #155724; border-radius: 12px; font-size: 12px;">Active</span>
                    @else
                        <span style="padding: 4px 12px; background: #f8d7da; color: #721c24; border-radius: 12px; font-size: 12px;">Inactive</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Employment Details --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Employment Details</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Address</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $employee->address ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Salary</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">₹{{ $employee->salary ? number_format($employee->salary, 2) : '0.00' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Joining Date</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $employee->joining_date ? $employee->joining_date->format('d M Y') : 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Manager</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $employee->manager ? $employee->manager->employee_name : 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Additional Information --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Additional Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created At</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $employee->created_at ? $employee->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Last Updated</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $employee->updated_at ? $employee->updated_at->format('d M Y, h:i A') : 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

