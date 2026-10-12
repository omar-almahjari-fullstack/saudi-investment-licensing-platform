<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CopyInvestorIdFrontBack extends Command
{
    protected $signature = 'data:copy-investor-id-front-back {--limit=0}';
    protected $description = 'Copy identity_image_front/back from investors to users for matching user_id when users fields are empty.';

    public function handle()
    {
        if (! Schema::hasTable('investors') || ! Schema::hasTable('users')) {
            $this->error('Required tables missing (investors or users).');
            return 1;
        }

        // Only consider investors that have front/back values
        $query = DB::table('investors')->whereNotNull('user_id')
            ->where(function($q){
                $q->whereNotNull('identity_image_front')->orWhereNotNull('identity_image_back');
            });

        $limit = (int) $this->option('limit');
        if ($limit > 0) { $query->limit($limit); }

        $rows = $query->get();
        $this->info('Found ' . $rows->count() . ' investors with front/back identity images.');
        $updated = 0;

        foreach ($rows as $inv) {
            $uid = $inv->user_id;
            $user = DB::table('users')->where('id', $uid)->first();
            if (! $user) { continue; }

            $updates = [];
            if (! empty($inv->identity_image_front)) {
                if (Schema::hasColumn('users','identity_image_front') && empty($user->identity_image_front)) {
                    $updates['identity_image_front'] = $inv->identity_image_front;
                }
            }
            if (! empty($inv->identity_image_back)) {
                if (Schema::hasColumn('users','identity_image_back') && empty($user->identity_image_back)) {
                    $updates['identity_image_back'] = $inv->identity_image_back;
                }
            }

            if (! empty($updates)) {
                DB::table('users')->where('id', $uid)->update($updates + ['updated_at' => now()]);
                $updated++;
                $this->line("Updated user {$uid} with: " . implode(', ', array_keys($updates)));
            }
        }

        $this->info('Completed. Users updated: ' . $updated);
        return 0;
    }
}
