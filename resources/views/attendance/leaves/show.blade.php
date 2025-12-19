@extends('layouts.dashboard')

@section('title', 'View Leave Request - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Leave Request Details</h2>
        <div style="display: flex; gap: 10px;">
            @php
                $user = auth()->user();
                $formName = $user->getFormNameFromRoute('leaves.index');
                $canWrite = $user->canWrite($formName);
            @endphp
            @if($canWrite)
                <a href="{{ route('leaves.edit', $leaf) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('leaves.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Employee Name</label>
            <div style="color: #333; font-size: 16px; font-weight: 500;">{{ $leaf->employee->employee_name ?? 'N/A' }}</div>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Leave Type</label>
            <div style="color: #333; font-size: 16px;">{{ $leaf->leaveType->name ?? 'N/A' }}</div>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Date</label>
            <div style="color: #333; font-size: 16px;">{{ optional($leaf->date)->format('d-m-Y') }}</div>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Status Change</label>
            <div style="color: #333; font-size: 16px;">
                @if($leaf->change_to_present)
                    <span style="padding: 6px 12px; background: #28a745; color: white; border-radius: 20px; font-size: 12px; font-weight: 500;">
                        <i class="fas fa-check"></i> Changed to Present
                    </span>
                @else
                    <span style="padding: 6px 12px; background: #6c757d; color: white; border-radius: 20px; font-size: 12px; font-weight: 500;">
                        Absent
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if($leaf->remarks)
    <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Remarks</label>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; color: #333;">{{ $leaf->remarks }}</div>
    </div>
    @endif
</div>
@endsection

