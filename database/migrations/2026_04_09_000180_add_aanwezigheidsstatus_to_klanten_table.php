<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('klanten')) {
            return;
        }

        if (! Schema::hasColumn('klanten', 'aanwezigheidsstatus')) {
            Schema::table('klanten', function (Blueprint $table): void {
                $table->string('aanwezigheidsstatus', 30)->default('binnen_land')->after('emailadres');
            });
        }

        DB::table('klanten')
            ->whereNull('aanwezigheidsstatus')
            ->update(['aanwezigheidsstatus' => 'binnen_land']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('klanten') || ! Schema::hasColumn('klanten', 'aanwezigheidsstatus')) {
            return;
        }

        Schema::table('klanten', function (Blueprint $table): void {
            $table->dropColumn('aanwezigheidsstatus');
        });
    }
};
