<?php
// app/Console/Commands/AutoArchiveSchedules.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kegiatan;
use Carbon\Carbon;

class AutoArchiveSchedules extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'schedule:auto-archive {--force : Force archive without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically archive outdated schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        // Find outdated schedules (before today and not already archived)
        $outdatedSchedules = Kegiatan::where('is_archived', false)
            ->where('tanggal', '<', $today)
            ->get();

        if ($outdatedSchedules->isEmpty()) {
            $this->info('No outdated schedules found to archive.');
            return 0;
        }

        $count = $outdatedSchedules->count();
        
        if (!$this->option('force')) {
            if (!$this->confirm("Found {$count} outdated schedule(s). Do you want to archive them?")) {
                $this->info('Archive operation cancelled.');
                return 0;
            }
        }

        // Archive each outdated schedule
        foreach ($outdatedSchedules as $schedule) {
            $schedule->update([
                'is_archived' => true,
                'archived_at' => Carbon::now()
            ]);
            
            $this->line("Archived: {$schedule->nama_kegiatan} (Date: {$schedule->tanggal})");
        }

        $this->info("Successfully archived {$count} outdated schedule(s).");
        
        return 0;
    }
}