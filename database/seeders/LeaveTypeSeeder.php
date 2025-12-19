<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Casual Leave',
                'description' => 'Casual leave for personal reasons',
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Leave for medical reasons',
                'is_active' => true,
            ],
            [
                'name' => 'Earned Leave',
                'description' => 'Earned leave based on service',
                'is_active' => true,
            ],
            [
                'name' => 'Compensatory Leave',
                'description' => 'Compensatory leave for overtime work',
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Maternity leave for female employees',
                'is_active' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Paternity leave for male employees',
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::firstOrCreate(
                ['name' => $leaveType['name']],
                $leaveType
            );
        }
    }
}
