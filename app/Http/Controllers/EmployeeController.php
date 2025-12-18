<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $employees = Employee::query()
            ->when($search, function ($query, $search) {
                $like = '%' . $search . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('employee_name', 'like', $like)
                        ->orWhere('code', 'like', $like)
                        ->orWhere('designation', 'like', $like)
                        ->orWhere('phone_number', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->orderBy('employee_name')
            ->paginate(15)
            ->withQueryString();

        return view('masters.employees.index', compact('employees', 'search'));
    }

    public function create()
    {
        $departments = $this->getDepartments();
        $managers = Employee::orderBy('employee_name')->get();

        return view('masters.employees.create', compact('departments', 'managers'));
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        $data = $request->only([
            'employee_name',
            'designation',
            'phone_number',
            'email',
            'address',
            'department',
            'salary',
            'joining_date',
            'manager_id',
        ]);

        $user = Auth::user();
        $data['organization_id'] = $user->organization_id ?? null;
        $data['branch_id'] = session('active_branch_id');
        $data['created_by'] = $user->id;
        $data['is_active'] = $request->boolean('is_active', true);

        // Generate unique employee code from name
        $baseCode = strtoupper(preg_replace('/[^A-Z0-9]/', '', substr($data['employee_name'], 0, 20)));
        if (empty($baseCode)) {
            $baseCode = 'EMP' . strtoupper(substr(md5($data['employee_name'] . microtime()), 0, 6));
        }

        $code = $baseCode;
        $suffix = 1;
        $maxAttempts = 50;

        while (Employee::withTrashed()->where('code', $code)->exists() && $maxAttempts > 0) {
            $code = $baseCode . $suffix;
            $suffix++;
            $maxAttempts--;
        }

        if ($maxAttempts === 0 && Employee::withTrashed()->where('code', $code)->exists()) {
            $code = $baseCode . '_' . time();
        }

        $data['code'] = $code;

        try {
            Employee::create($data);
        } catch (QueryException $e) {
            if ((int) $e->getCode() === 23000) {
                $data['code'] = $baseCode . '_' . time() . '_' . random_int(100, 999);
                Employee::create($data);
            } else {
                throw $e;
            }
        }

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        return view('masters.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = $this->getDepartments();
        $managers = Employee::where('id', '!=', $employee->id)
            ->orderBy('employee_name')
            ->get();

        return view('masters.employees.edit', compact('employee', 'departments', 'managers'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->validateRequest($request, $employee->id);

        $data = $request->only([
            'employee_name',
            'designation',
            'phone_number',
            'email',
            'address',
            'department',
            'salary',
            'joining_date',
            'manager_id',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $employee->update($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Common validation rules and messages.
     */
    protected function validateRequest(Request $request, ?int $employeeId = null): void
    {
        $emailRule = 'nullable|email|max:255';
        if ($employeeId) {
            $emailRule .= '|unique:employees,email,' . $employeeId;
        } else {
            $emailRule .= '|unique:employees,email';
        }

        $request->validate(
            [
                'employee_name' => ['required', 'string', 'max:255'],
                'designation' => ['nullable', 'string', 'max:255'],
                'phone_number' => ['nullable', 'string', 'max:20'],
                // Use string rule (with pipes) instead of single-element array to avoid BadMethodCallException
                'email' => $emailRule,
                'address' => ['nullable', 'string', 'max:500'],
                'department' => ['nullable', 'in:sales,hr,operations,accounts,production,it,other'],
                'salary' => ['nullable', 'numeric', 'min:0'],
                'joining_date' => ['nullable', 'date'],
                'manager_id' => ['nullable', 'exists:employees,id'],
            ],
            [
                'employee_name.required' => 'The Employee Name field is required.',
                'employee_name.max' => 'The Employee Name may not be greater than 255 characters.',
                'designation.max' => 'The Designation may not be greater than 255 characters.',
                'phone_number.max' => 'The Phone Number may not be greater than 20 characters.',
                'email.email' => 'The Email must be a valid email address.',
                'email.unique' => 'The Email has already been taken.',
                'address.max' => 'The Address may not be greater than 500 characters.',
                'department.in' => 'The Department must be one of the allowed options.',
                'salary.numeric' => 'The Salary must be a number.',
                'salary.min' => 'The Salary must be at least 0.',
                'joining_date.date' => 'The Joining Date must be a valid date.',
                'manager_id.exists' => 'The selected Manager is invalid.',
            ]
        );
    }

    /**
     * Department options for dropdown.
     */
    protected function getDepartments(): array
    {
        return [
            'sales' => 'Sales',
            'hr' => 'HR',
            'operations' => 'Operations',
            'accounts' => 'Accounts',
            'production' => 'Production',
            'it' => 'IT',
            'other' => 'Other',
        ];
    }
}
