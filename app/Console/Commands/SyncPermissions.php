<?php

namespace App\Console\Commands;

use App\Services\PermissionSyncService;
use Illuminate\Console\Command;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync {--force : Force sync even if permissions exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions from application routes';

    /**
     * Execute the console command.
     */
    public function handle(PermissionSyncService $service)
    {
        $this->info('Syncing permissions from routes...');
        $this->newLine();

        $result = $service->syncFromRoutes();

        $this->info('✓ Sync completed!');
        $this->newLine();

        if (!empty($result['created'])) {
            $this->info('Created permissions: ' . count($result['created']));
            foreach ($result['created'] as $form) {
                $this->line("  + {$form}");
            }
            $this->newLine();
        }

        if (!empty($result['updated'])) {
            $this->info('Updated permissions: ' . count($result['updated']));
            foreach ($result['updated'] as $form) {
                $this->line("  ~ {$form}");
            }
            $this->newLine();
        }

        if (!empty($result['skipped'])) {
            $this->warn('Skipped routes (no module mapping): ' . count($result['skipped']));
            foreach ($result['skipped'] as $route) {
                $this->line("  - {$route}");
            }
            $this->newLine();
        }

        $this->info('Total permissions in database: ' . \App\Models\Permission::count());
        
        return Command::SUCCESS;
    }
}

