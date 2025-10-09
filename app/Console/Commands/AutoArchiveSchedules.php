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
    protected $description = 'Automatically archive outdated schedules (where tanggal_selesai < today)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $this->info("Running auto-archive at: " . Carbon::now()->toDateTimeString());
        $this->info("Today's date: " . $today);
        
        // Find outdated schedules (tanggal_selesai before today and not already archived)
        $outdatedSchedules = Kegiatan::where('is_archived', false)
            ->where('tanggal_selesai', '<', $today)
            ->get();

        if ($outdatedSchedules->isEmpty()) {
            $this->info('No outdated schedules found to archive.');
            return 0;
        }

        $count = $outdatedSchedules->count();
        
        $this->warn("Found {$count} outdated schedule(s) to archive:");
        
        // Display schedules that will be archived
        foreach ($outdatedSchedules as $schedule) {
            $this->line("  - {$schedule->nama_kegiatan} (End date: {$schedule->tanggal_selesai->format('Y-m-d')})");
        }
        
        if (!$this->option('force')) {
            if (!$this->confirm("\nDo you want to archive these schedules?")) {
                $this->info('Archive operation cancelled.');
                return 0;
            }
        }

        // Archive each outdated schedule
        $archived = 0;
        foreach ($outdatedSchedules as $schedule) {
            try {
                $schedule->update([
                    'is_archived' => true,
                    'archived_at' => Carbon::now()
                ]);
                
                $this->info("✓ Archived: {$schedule->nama_kegiatan}");
                $archived++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to archive: {$schedule->nama_kegiatan} - {$e->getMessage()}");
            }
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("Successfully archived {$archived} out of {$count} outdated schedule(s).");
        $this->info(str_repeat('=', 50));
        
        return 0;
    }
}