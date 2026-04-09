<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'klant')
            ->update(['role' => User::ROLE_VRIJWILLIGER]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geen rollback nodig voor data-correctie.
    }
};
