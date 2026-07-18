<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lead_statuses', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('is_active');
        });

        $firstStatusId = DB::table('lead_statuses')->orderBy('id')->value('id');

        if ($firstStatusId !== null) {
            DB::table('lead_statuses')
                ->where('id', $firstStatusId)
                ->update(['is_default' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_statuses', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
