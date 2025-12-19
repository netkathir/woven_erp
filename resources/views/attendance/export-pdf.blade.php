<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .filters p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #667eea;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            margin-top: 0;
            color: #333;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Report</h1>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
        @if($request->filled('start_date') || $request->filled('end_date'))
            <p>Period: 
                {{ $request->filled('start_date') ? date('d-m-Y', strtotime($request->start_date)) : 'All' }} 
                to 
                {{ $request->filled('end_date') ? date('d-m-Y', strtotime($request->end_date)) : 'All' }}
            </p>
        @endif
        @if($request->filled('employee_id'))
            <p>Employee: {{ $attendances->first()->employee->employee_name ?? 'N/A' }}</p>
        @endif
        @if($request->filled('status'))
            <p>Status: {{ $request->status }}</p>
        @endif
    </div>

    @if($attendances->count() > 0)
        <div class="summary">
            <h3>Summary</h3>
            <p><strong>Total Records:</strong> {{ $attendances->count() }}</p>
            <p><strong>Total Present:</strong> {{ $totalPresent }}</p>
            <p><strong>Total Absent:</strong> {{ $totalAbsent }}</p>
        </div>

        <h3>Detailed Report</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Employee Name</th>
                    <th>Code</th>
                    <th>Department</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ optional($attendance->date)->format('d-m-Y') }}</td>
                        <td>{{ $attendance->employee->employee_name ?? 'N/A' }}</td>
                        <td>{{ $attendance->employee->code ?? 'N/A' }}</td>
                        <td>{{ $attendance->employee->department ?? '-' }}</td>
                        <td class="text-center">{{ $attendance->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 40px; color: #666;">
            No data found for the selected filters.
        </p>
    @endif
</body>
</html>

