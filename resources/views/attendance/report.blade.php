@extends('layouts.dashboard')

@section('title', 'Attendance Report - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('attendances.index');
    $canRead = $user->canRead($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Attendance Report</h2>
        <a href="{{ route('attendances.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> Back to Attendance
        </a>
    </div>

    {{-- Filter Form --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
        <form method="GET" action="{{ route('attendances.report') }}" id="reportForm">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label for="start_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                </div>

                <div>
                    <label for="end_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                </div>

                <div>
                    <label for="employee_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Employee</label>
                    <select name="employee_id" id="employee_id"
                            style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_name }} ({{ $employee->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Status</label>
                    <select name="status" id="status"
                            style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                        <option value="">All Status</option>
                        <option value="Present" {{ request('status') === 'Present' ? 'selected' : '' }}>Present</option>
                        <option value="Absent" {{ request('status') === 'Absent' ? 'selected' : '' }}>Absent</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-search"></i> Generate Report
                </button>
                <button type="button" onclick="exportPdf()" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button type="button" onclick="exportExcel()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                @if(request()->anyFilled(['start_date', 'end_date', 'employee_id', 'status']))
                    <a href="{{ route('attendances.report') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center;">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($attendances->count() > 0)
        {{-- Summary Cards --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px; color: white;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Records</div>
                <div style="font-size: 32px; font-weight: 700;">{{ $totalRecords }}</div>
            </div>
            <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 20px; border-radius: 8px; color: white;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Present</div>
                <div style="font-size: 32px; font-weight: 700;">{{ $totalPresent }}</div>
            </div>
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; border-radius: 8px; color: white;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Absent</div>
                <div style="font-size: 32px; font-weight: 700;">{{ $totalAbsent }}</div>
            </div>
        </div>

        {{-- Report Table --}}
        <div style="margin-bottom: 25px;">
            <h3 style="color: #333; font-size: 18px; margin-bottom: 15px;">Attendance Details</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Date</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Employee Name</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Code</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Department</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; color: #666;">{{ optional($attendance->date)->format('d-m-Y') }}</td>
                                <td style="padding: 12px; color: #333; font-weight: 500;">{{ $attendance->employee->employee_name ?? 'N/A' }}</td>
                                <td style="padding: 12px; color: #666;">{{ $attendance->employee->code ?? 'N/A' }}</td>
                                <td style="padding: 12px; color: #333;">{{ $attendance->employee->department ?? '-' }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($attendance->status === 'Present')
                                        <span style="padding: 6px 12px; background: #28a745; color: white; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                            <i class="fas fa-check"></i> Present
                                        </span>
                                    @else
                                        <span style="padding: 6px 12px; background: #dc3545; color: white; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                            <i class="fas fa-times"></i> Absent
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-chart-line" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
            <p style="font-size: 18px; margin-bottom: 10px;">No data found for the selected filters.</p>
            <p style="font-size: 14px; color: #999;">Please adjust your filters and try again.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function exportPdf() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        window.open('{{ route("attendances.export.pdf") }}?' + params.toString(), '_blank');
    }

    function exportExcel() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        window.location.href = '{{ route("attendances.export.excel") }}?' + params.toString();
    }
</script>
@endpush
@endsection

