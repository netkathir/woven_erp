<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ResetSuperAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password {--email=superadmin@crm.com} {--password=SuperAdmin@123}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Super Admin password without deleting users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('==========================================');
        $this->info('Resetting Super Admin Password');
        $this->info('==========================================');
        $this->newLine();

        $email = $this->option('email');
        $password = $this->option('password');

        try {
            DB::beginTransaction();

            // Get Super Admin role
            $superAdminRole = Role::where('slug', 'super-admin')->first();
            
            if (!$superAdminRole) {
                $this->error('❌ Super Admin role not found. Please run RoleSeeder first.');
                return 1;
            }

            // Find or create Super Admin user
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->info("User with email '{$email}' not found. Creating new Super Admin user...");
                
                $userData = [
                    'name' => 'Super Admin',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role_id' => $superAdminRole->id,
                    'mobile' => null,
                    'organization_id' => null,
                    'branch_id' => null,
                    'entity_id' => null,
                ];
                
                // Add status if column exists
                if (\Schema::hasColumn('users', 'status')) {
                    $userData['status'] = 'active';
                }
                if (\Schema::hasColumn('users', 'created_by')) {
                    $userData['created_by'] = null;
                }
                
                $user = User::create($userData);
                $this->info("✅ Super Admin user created!");
            } else {
                // Update password
                $user->password = Hash::make($password);
                $user->role_id = $superAdminRole->id;
                
                // Ensure user is active
                if (\Schema::hasColumn('users', 'status')) {
                    $user->status = 'active';
                }
                
                $user->save();
                $this->info("✅ Super Admin password reset!");
            }

            // Ensure Super Admin has Main Branch assigned
            $mainBranch = Branch::where('code', 'MB001')->first();
            if ($mainBranch) {
                $user->branches()->sync([$mainBranch->id]);
                $this->info("✅ Assigned to Main Branch: {$mainBranch->name}");
            } else {
                $this->warn("⚠️  Main Branch (MB001) not found. Please run BranchSeeder first.");
            }

            DB::commit();

            $this->newLine();
            $this->info('==========================================');
            $this->info('✅ Password Reset Successful!');
            $this->info('==========================================');
            $this->newLine();
            $this->info('📧 Login Credentials:');
            $this->line('   Email: ' . $email);
            $this->line('   Password: ' . $password);
            $this->newLine();
            $this->info('🔐 Please save these credentials securely!');
            $this->newLine();

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}

