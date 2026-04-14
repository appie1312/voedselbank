<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Unit\Support\CreatesModelTestSchema;

class AccountTest extends TestCase
{
    use CreatesModelTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createModelTestSchema();
    }

    public function test_account_overzicht_lijst_geeft_resultaat(): void
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Directie Demo',
            'email' => 'directie@voedselbank.local',
            'role' => User::ROLE_DIRECTIE,
            'password' => 'secret',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => 1,
            'telefoon' => '06-12345678',
            'afdeling' => 'Bestuur',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $lijst = Account::overzichtLijst();

        $this->assertCount(1, $lijst);
        $this->assertSame('Directie Demo', $lijst->first()->name);
    }
}

