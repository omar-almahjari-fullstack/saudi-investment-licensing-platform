<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VerifyIdentityCopy extends Command
{
    protected $signature = 'data:verify-identity-copy';
    protected $description = 'Verify counts and samples for identity fields between investors and users.';

    public function handle()
    {
        if (! Schema::hasTable('investors')) { $this->error('investors table missing'); return 1; }
        if (! Schema::hasTable('users')) { $this->error('users table missing'); return 1; }

        // Determine which identity columns still exist on investors/users
        $investorCols = array_filter(['identity_image', 'identity_image_front', 'identity_image_back'], function($c){ return Schema::hasColumn('investors', $c); });
        $userCols = array_filter(['identity_image', 'identity_image_front', 'identity_image_back'], function($c){ return Schema::hasColumn('users', $c); });

        if (empty($investorCols)) {
            $countInvestors = 0;
            $this->info("Investors rows with any identity field: $countInvestors (no identity columns on investors)");
        } else {
            $countInvestors = DB::table('investors')
                ->where(function($q) use ($investorCols){
                    foreach ($investorCols as $col) {
                        $q->orWhereNotNull($col);
                    }
                })->count();
            $this->info("Investors rows with any identity field: $countInvestors");
        }

        if (empty($userCols)) {
            $countUsers = 0;
            $this->info("Users rows with any identity field: $countUsers (no identity columns on users)");
        } else {
            $countUsers = DB::table('users')
                ->where(function($q) use ($userCols){
                    foreach ($userCols as $col) {
                        $q->orWhereNotNull($col);
                    }
                })->count();
            $this->info("Users rows with any identity field: $countUsers");
        }

        $this->info('Sample investor -> user mappings (first 10):');
        if (empty($investorCols)) {
            $this->info('(no identity columns present on investors to sample)');
        } else {
            $samplesQuery = DB::table('investors')->whereNotNull('user_id')->where(function($q) use ($investorCols){
                foreach ($investorCols as $col) { $q->orWhereNotNull($col); }
            })->limit(10);
            $samples = $samplesQuery->get();
            foreach ($samples as $s) {
                $front = in_array('identity_image_front', $investorCols) ? ($s->identity_image_front ?? null) : null;
                $back  = in_array('identity_image_back', $investorCols) ? ($s->identity_image_back ?? null) : null;
                $this->line("investor id={$s->id} user_id={$s->user_id} front={$front} back={$back}");
            }
        }

        $this->info('Done.');
        return 0;
    }
}
