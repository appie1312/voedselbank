<?php

namespace Tests\Unit;

use App\Models\UserProfile;
use Tests\TestCase;
use Tests\Unit\Support\CreatesModelTestSchema;

class UserProfileTest extends TestCase
{
    use CreatesModelTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createModelTestSchema();
    }

    public function test_user_profile_heeft_relatie_naar_user(): void
    {
        $relatie = (new UserProfile())->user();

        $this->assertSame('user_id', $relatie->getForeignKeyName());
        $this->assertSame('id', $relatie->getOwnerKeyName());
    }
}

