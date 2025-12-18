<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_name',
        'assigned_to',
        'due_date',
        'priority',
        'status',
        'related_customer_id',
        'task_description',
        'task_type',
        'external_agency',
        'comments_updates',
        'is_recurring',
        'repeat_interval',
        'recurring_end_date',
        'notification_enabled',
        'created_by',
        'organization_id',
        'branch_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'recurring_end_date' => 'date',
        'is_recurring' => 'boolean',
        'notification_enabled' => 'boolean',
    ];

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function relatedCustomer()
    {
        return $this->belongsTo(Customer::class, 'related_customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

