<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('lead_status_id')->nullable()->after('phone')->constrained()->nullOnDelete();
        });

        $statusValues = DB::table('leads')
            ->whereNotNull('status')
            ->distinct()
            ->pluck('status');

        foreach ($statusValues as $statusValue) {
            $name = Str::title(str_replace('_', ' ', (string) $statusValue));

            $statusId = DB::table('lead_statuses')->where('name', $name)->value('id');

            if ($statusId === null) {
                $statusId = DB::table('lead_statuses')->insertGetId([
                    'name' => $name,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('leads')
                ->where('status', $statusValue)
                ->update(['lead_status_id' => $statusId]);
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('status')->default('new')->after('phone')->index();
        });

        $statuses = DB::table('lead_statuses')->pluck('name', 'id');

        foreach ($statuses as $id => $name) {
            DB::table('leads')
                ->where('lead_status_id', $id)
                ->update([
                    'status' => Str::of($name)->lower()->replace(' ', '_')->toString(),
                ]);
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_status_id');
        });
    }
};
