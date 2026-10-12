<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;

class MakeUserFolders extends Command
{
    protected $signature = 'storage:make-user-folders {--force : recreate existing folders}';
    protected $description = 'Create storage/app/public/users/{username_slug} for all users (fallback to id when username missing).';

    public function handle()
    {
        $this->info('Scanning users...');
        $count = 0;
        foreach (User::cursor() as $user) {
            $slug = null;
            if (! empty($user->username)) { $slug = Str::slug($user->username); }
            if (empty($slug)) { $slug = $user->id; }
            $dir = 'users/' . $slug;
            try {
                if (Storage::disk('public')->exists($dir)) {
                    if ($this->option('force')) {
                        Storage::disk('public')->deleteDirectory($dir);
                        Storage::disk('public')->makeDirectory($dir);
                        Storage::disk('public')->put($dir . '/.gitkeep', "");
                        $this->line("Recreated: $dir");
                        $count++;
                    } else {
                        // already exists, skip
                        continue;
                    }
                } else {
                    Storage::disk('public')->makeDirectory($dir);
                    Storage::disk('public')->put($dir . '/.gitkeep', "");
                    $this->line("Created: $dir");
                    $count++;
                }
            } catch (\Throwable $e) {
                $this->error("Failed: $dir — " . $e->getMessage());
            }
        }

        $this->info("Done. Folders created/recreated: $count");
        return 0;
    }
}
