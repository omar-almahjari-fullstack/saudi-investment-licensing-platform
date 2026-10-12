<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CopyInvestorIdentityToUsers extends Command
{
    protected $signature = 'data:copy-investor-identity {--limit=0}';
    protected $description = 'Copy identity_image fields from investors to users by user_id (write only to empty user fields).';

    public function handle()
    {
        if (! Schema::hasTable('investors') || ! Schema::hasTable('users')) {
            $this->error('Required tables missing (investors or users).');
            return 1;
        }

        $query = DB::table('investors')->whereNotNull('user_id')
            ->where(function($q){
                $q->whereNotNull('identity_image')->orWhereNotNull('identity_image_front')->orWhereNotNull('identity_image_back');
            });

        $limit = (int) $this->option('limit');
        if ($limit > 0) { $query->limit($limit); }

        $rows = $query->get();
        $updated = 0;
        $this->info('Found ' . $rows->count() . ' investors with identity data to consider.');

        foreach ($rows as $inv) {
            $uid = $inv->user_id;
            $user = DB::table('users')->where('id', $uid)->first();
            if (! $user) { continue; }

            $updates = [];

            if (! empty($inv->identity_image) && (empty($user->identity_image) || ! Schema::hasColumn('users','identity_image'))) {
                if (Schema::hasColumn('users','identity_image') && empty($user->identity_image)) {
                    $updates['identity_image'] = $inv->identity_image;
                }
            }
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
                DB::table('users')->where('id', $uid)->update($updates);
                $updated++;
                $this->line("Updated user: $uid with fields: " . implode(',', array_keys($updates)));
            }
        }

        $this->info('Completed. Users updated: ' . $updated);
        return 0;
    }
}
