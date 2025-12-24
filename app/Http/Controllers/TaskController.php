<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Employee;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['assignedEmployee', 'relatedCustomer', 'creator'])->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('task_name', 'like', "%{$search}%")
                    ->orWhere('task_description', 'like', "%{$search}%")
                    ->orWhereHas('relatedCustomer', function ($q) use ($search) {
                        $q->where('customer_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        if ($assignedTo = $request->get('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }

        $tasks = $query->paginate(15)->withQueryString();
        $employees = Employee::orderBy('employee_name')->get();

        return view('crm.tasks.index', compact('tasks', 'employees'));
    }

    public function create()
    {
        $employees = Employee::orderBy('employee_name')->get();
        $customers = Customer::orderBy('customer_name')->get();
        return view('crm.tasks.create', compact('employees', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task_name' => ['required', 'string', 'max:191'],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'due_date' => ['required', 'date'],
            'priority' => ['required', 'in:high,medium,low'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'related_customer_id' => ['nullable', 'exists:customers,id'],
            'task_description' => ['nullable', 'string'],
            'task_type' => ['nullable', 'string', 'max:191'],
            'external_agency' => ['nullable', 'string', 'max:191'],
            'comments_updates' => ['nullable', 'string'],
            'is_recurring' => ['nullable', 'boolean'],
            'repeat_interval' => ['nullable', 'in:daily,weekly,monthly'],
            'recurring_end_date' => ['nullable', 'date'],
            'notification_enabled' => ['nullable', 'boolean'],
        ]);

        $task = new Task();
        $task->fill($data);
        $task->is_recurring = $request->has('is_recurring');
        $task->notification_enabled = $request->has('notification_enabled');

        $user = Auth::user();
        if ($user) {
            $task->organization_id = $user->organization_id ?? null;
            $task->branch_id = session('active_branch_id');
            $task->created_by = $user->id;
        }

        $task->save();

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $task->load(['assignedEmployee', 'relatedCustomer', 'creator']);
        return view('crm.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $employees = Employee::orderBy('employee_name')->get();
        $customers = Customer::orderBy('customer_name')->get();
        return view('crm.tasks.edit', compact('task', 'employees', 'customers'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'task_name' => ['required', 'string', 'max:191'],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'due_date' => ['required', 'date'],
            'priority' => ['required', 'in:high,medium,low'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'related_customer_id' => ['nullable', 'exists:customers,id'],
            'task_description' => ['nullable', 'string'],
            'task_type' => ['nullable', 'string', 'max:191'],
            'external_agency' => ['nullable', 'string', 'max:191'],
            'comments_updates' => ['nullable', 'string'],
            'is_recurring' => ['nullable', 'boolean'],
            'repeat_interval' => ['nullable', 'in:daily,weekly,monthly'],
            'recurring_end_date' => ['nullable', 'date'],
            'notification_enabled' => ['nullable', 'boolean'],
        ]);

        $task->fill($data);
        $task->is_recurring = $request->has('is_recurring');
        $task->notification_enabled = $request->has('notification_enabled');
        $task->save();

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}

