<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Tests\Unit\Support\CreatesModelTestSchema;

class UserTest extends TestCase
{
    use CreatesModelTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createModelTestSchema();
    }

    public function test_user_role_helpers_worken(): void
    {
        $this->assertTrue((new User(['role' => User::ROLE_DIRECTIE]))->isDirectie());
        $this->assertTrue((new User(['role' => User::ROLE_MAGAZIJN_MEDEWERKER]))->isMagazijnMedewerker());
        $this->assertTrue((new User(['role' => User::ROLE_VRIJWILLIGER]))->isVrijwilliger());
    }
}

