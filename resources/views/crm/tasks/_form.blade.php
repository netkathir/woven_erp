@php
    $editing = isset($task);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="task_name" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Task Name <span style="color:red">*</span></label>
        <input type="text" name="task_name" id="task_name" required
               value="{{ old('task_name', $editing ? $task->task_name : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('task_name')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="assigned_to" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Assigned To</label>
        <select name="assigned_to" id="assigned_to"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Employee --</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" 
                        {{ old('assigned_to', $editing ? $task->assigned_to : '') == $employee->id ? 'selected' : '' }}>
                    {{ $employee->employee_name }}
                </option>
            @endforeach
        </select>
        @error('assigned_to')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="due_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Due Date <span style="color:red">*</span></label>
        <input type="date" name="due_date" id="due_date" required
               value="{{ old('due_date', $editing ? optional($task->due_date)->format('Y-m-d') : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('due_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="priority" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Priority <span style="color:red">*</span></label>
        <select name="priority" id="priority" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="high" {{ old('priority', $editing ? $task->priority : 'medium') === 'high' ? 'selected' : '' }}>High</option>
            <option value="medium" {{ old('priority', $editing ? $task->priority : 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="low" {{ old('priority', $editing ? $task->priority : 'medium') === 'low' ? 'selected' : '' }}>Low</option>
        </select>
        @error('priority')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="status" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Status <span style="color:red">*</span></label>
        <select name="status" id="status" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="pending" {{ old('status', $editing ? $task->status : 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ old('status', $editing ? $task->status : 'pending') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ old('status', $editing ? $task->status : 'pending') === 'completed' ? 'selected' : '' }}>Completed</option>
        </select>
        @error('status')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="related_customer_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Related Customer</label>
        <select name="related_customer_id" id="related_customer_id"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Customer --</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" 
                        {{ old('related_customer_id', $editing ? $task->related_customer_id : '') == $customer->id ? 'selected' : '' }}>
                    {{ $customer->customer_name }}
                </option>
            @endforeach
        </select>
        @error('related_customer_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="task_type" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Task Type</label>
        <input type="text" name="task_type" id="task_type"
               value="{{ old('task_type', $editing ? $task->task_type : '') }}"
               placeholder="e.g., Follow-up, Meeting, Call"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('task_type')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="external_agency" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">External Agency</label>
        <input type="text" name="external_agency" id="external_agency"
               value="{{ old('external_agency', $editing ? $task->external_agency : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('external_agency')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
</div>

<div style="margin-bottom: 20px;">
    <label for="task_description" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Task Description</label>
    <textarea name="task_description" id="task_description" rows="4"
              style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-family: inherit;">{{ old('task_description', $editing ? $task->task_description : '') }}</textarea>
    @error('task_description')
        <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
    @enderror
</div>

<div style="margin-bottom: 20px;">
    <label for="comments_updates" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Comments/Updates</label>
    <textarea name="comments_updates" id="comments_updates" rows="4"
              style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-family: inherit;">{{ old('comments_updates', $editing ? $task->comments_updates : '') }}</textarea>
    @error('comments_updates')
        <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
    @enderror
</div>

<h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Recurring Task Options</h3>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <input type="checkbox" name="is_recurring" id="is_recurring" value="1"
                   {{ old('is_recurring', $editing && $task->is_recurring ? 'checked' : '') }}
                   onchange="toggleRecurringOptions()">
            <span style="font-weight: 600; color: #333;">Recurring Task</span>
        </label>
    </div>

    <div id="recurring_options" style="display: {{ old('is_recurring', $editing && $task->is_recurring ? 'block' : 'none') }};">
        <label for="repeat_interval" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Repeat Interval</label>
        <select name="repeat_interval" id="repeat_interval"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Interval --</option>
            <option value="daily" {{ old('repeat_interval', $editing ? $task->repeat_interval : '') === 'daily' ? 'selected' : '' }}>Daily</option>
            <option value="weekly" {{ old('repeat_interval', $editing ? $task->repeat_interval : '') === 'weekly' ? 'selected' : '' }}>Weekly</option>
            <option value="monthly" {{ old('repeat_interval', $editing ? $task->repeat_interval : '') === 'monthly' ? 'selected' : '' }}>Monthly</option>
        </select>
        @error('repeat_interval')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div id="recurring_end_date_div" style="display: {{ old('is_recurring', $editing && $task->is_recurring ? 'block' : 'none') }};">
        <label for="recurring_end_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">End Date</label>
        <input type="date" name="recurring_end_date" id="recurring_end_date"
               value="{{ old('recurring_end_date', $editing ? optional($task->recurring_end_date)->format('Y-m-d') : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('recurring_end_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <input type="checkbox" name="notification_enabled" id="notification_enabled" value="1"
                   {{ old('notification_enabled', $editing && $task->notification_enabled ? 'checked' : '') }}>
            <span style="font-weight: 600; color: #333;">Enable Notifications</span>
        </label>
    </div>
</div>

@push('scripts')
<script>
    function toggleRecurringOptions() {
        var isRecurring = document.getElementById('is_recurring').checked;
        document.getElementById('recurring_options').style.display = isRecurring ? 'block' : 'none';
        document.getElementById('recurring_end_date_div').style.display = isRecurring ? 'block' : 'none';
    }
</script>
@endpush

