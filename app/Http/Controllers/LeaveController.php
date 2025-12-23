<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['employee', 'leaveType'])->orderByDesc('date');

        if ($search = $request->get('search')) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $leaves = $query->paginate(15)->withQueryString();

        return view('attendance.leaves.index', compact('leaves'));
    }

    public function create()
    {
        $employees = Employee::orderBy('employee_name')->get();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();
        
        // Get default leave type (Casual Leave)
        $defaultLeaveType = LeaveType::where('name', 'Casual Leave')->first();
        
        return view('attendance.leaves.create', compact('employees', 'leaveTypes', 'defaultLeaveType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'date' => ['required', 'date'],
            'change_to_present' => ['nullable', 'boolean'],
            'remarks' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $organizationId = $user->organization_id ?? null;
        $branchId = session('active_branch_id');
        $createdBy = $user->id;

        $leave = Leave::create([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'date' => $request->date,
            'change_to_present' => $request->has('change_to_present'),
            'remarks' => $request->remarks,
            'organization_id' => $organizationId,
            'branch_id' => $branchId,
            'created_by' => $createdBy,
        ]);

        // If change_to_present is true, update attendance
        if ($request->has('change_to_present')) {
            $attendance = Attendance::where('date', $request->date)
                ->where('employee_id', $request->employee_id)
                ->first();

            if ($attendance) {
                $attendance->update(['status' => 'Present']);
            } else {
                // Create new attendance record if it doesn't exist
                Attendance::create([
                    'date' => $request->date,
                    'employee_id' => $request->employee_id,
                    'status' => 'Present',
                    'organization_id' => $organizationId,
                    'branch_id' => $branchId,
                    'created_by' => $createdBy,
                ]);
            }
        } else {
            // If not changing to present, mark as absent in attendance
            $attendance = Attendance::where('date', $request->date)
                ->where('employee_id', $request->employee_id)
                ->first();

            if ($attendance) {
                $attendance->update(['status' => 'Absent']);
            } else {
                // Create new attendance record if it doesn't exist
                Attendance::create([
                    'date' => $request->date,
                    'employee_id' => $request->employee_id,
                    'status' => 'Absent',
                    'organization_id' => $organizationId,
                    'branch_id' => $branchId,
                    'created_by' => $createdBy,
                ]);
            }
        }

        return redirect()->route('leaves.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    public function show(Leave $leaf)
    {
        $leaf->load(['employee', 'leaveType']);
        return view('attendance.leaves.show', compact('leaf'));
    }

    public function edit(Leave $leaf)
    {
        $employees = Employee::orderBy('employee_name')->get();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();
        
        return view('attendance.leaves.edit', compact('leaf', 'employees', 'leaveTypes'));
    }

    public function update(Request $request, Leave $leaf)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'date' => ['required', 'date'],
            'change_to_present' => ['nullable', 'boolean'],
            'remarks' => ['nullable', 'string'],
        ]);

        $leaf->update([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'date' => $request->date,
            'change_to_present' => $request->has('change_to_present'),
            'remarks' => $request->remarks,
        ]);

        // Update attendance based on change_to_present
        $attendance = Attendance::where('date', $request->date)
            ->where('employee_id', $request->employee_id)
            ->first();

        $user = Auth::user();
        $organizationId = $user->organization_id ?? null;
        $branchId = session('active_branch_id');
        $createdBy = $user->id;

        if ($request->has('change_to_present')) {
            if ($attendance) {
                $attendance->update(['status' => 'Present']);
            } else {
                Attendance::create([
                    'date' => $request->date,
                    'employee_id' => $request->employee_id,
                    'status' => 'Present',
                    'organization_id' => $organizationId,
                    'branch_id' => $branchId,
                    'created_by' => $createdBy,
                ]);
            }
        } else {
            if ($attendance) {
                $attendance->update(['status' => 'Absent']);
            } else {
                Attendance::create([
                    'date' => $request->date,
                    'employee_id' => $request->employee_id,
                    'status' => 'Absent',
                    'organization_id' => $organizationId,
                    'branch_id' => $branchId,
                    'created_by' => $createdBy,
                ]);
            }
        }

        return redirect()->route('leaves.index')
            ->with('success', 'Leave request updated successfully.');
    }

    public function destroy(Leave $leaf)
    {
        $leaf->delete();

        return redirect()->route('leaves.index')
            ->with('success', 'Leave request deleted successfully.');
    }
}
