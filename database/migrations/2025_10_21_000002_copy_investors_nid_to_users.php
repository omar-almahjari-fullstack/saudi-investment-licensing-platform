<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CopyInvestorsNidToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * Copies any non-empty investors.national_id into users.national_id where users value is empty.
     * This is idempotent and non-destructive (does not drop investor column).
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('users', 'national_id')) {
            // nothing to do
            return;
        }
        if (! Schema::hasColumn('investors', 'national_id')) {
            // nothing to copy
            return;
        }

        // Copy where users.national_id is empty/null and investors.national_id is present
        $rows = DB::table('users as u')
            ->join('investors as i', 'i.user_id', '=', 'u.id')
            ->where(function($q){
                $q->whereNull('u.national_id')->orWhereRaw('TRIM(u.national_id) = ""');
            })
            ->whereNotNull('i.national_id')
            ->whereRaw('TRIM(i.national_id) <> ""')
            ->select('u.id as user_id', 'i.national_id as nid')
            ->get();

        foreach ($rows as $r) {
            DB::table('users')->where('id', $r->user_id)->update(['national_id' => $r->nid]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Not reversible (we won't clear users.national_id automatically)
    }
}
