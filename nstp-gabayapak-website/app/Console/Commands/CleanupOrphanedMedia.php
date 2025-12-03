<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\ActivityUpdatePicture;
use App\Models\Staff;

class CleanupOrphanedMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:cleanup {--dry-run : Do not delete files, only list what would be removed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove orphaned media files that are not referenced in the database (project logos, proof pictures, staff formal pictures).';

    public function handle()
    {
        $this->info('Starting orphaned media cleanup...');
        $dry = $this->option('dry-run');

        // Build referenced sets
        $projectLogos = Project::whereNotNull('Project_Logo')->pluck('Project_Logo')->filter()->values()->all();
        $proofPaths = ActivityUpdatePicture::whereNotNull('path')->pluck('path')->filter()->values()->all();
        $staffPics = Staff::whereNotNull('staff_formal_picture')->pluck('staff_formal_picture')->filter()->values()->all();

        $referenced = array_merge($projectLogos, $proofPaths, $staffPics);
        $referenced = array_values(array_unique($referenced));

        $dirs = [
            'project_logos',
            'proof_pictures',
            'staff_formal_pictures',
            // add other folders here if you use different names
        ];

        $deleted = 0;
        foreach ($dirs as $dir) {
            $this->info("Scanning: $dir");
            $files = Storage::disk('public')->allFiles($dir);
            foreach ($files as $f) {
                // Normalize path
                $rel = ltrim($f, '/');
                if (!in_array($rel, $referenced, true)) {
                    if ($dry) {
                        $this->line("[DRY] Orphan: $rel");
                    } else {
                        if (Storage::disk('public')->delete($rel)) {
                            $this->line("Deleted: $rel");
                            $deleted++;
                        } else {
                            $this->error("Failed to delete: $rel");
                        }
                    }
                }
            }
        }

        $this->info($dry ? 'Dry run complete.' : "Cleanup complete. Files deleted: $deleted");
        return 0;
    }
}
