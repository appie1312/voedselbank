<?php

namespace Tests\Unit;

use App\Models\Klant;
use Tests\TestCase;
use Tests\Unit\Support\CreatesModelTestSchema;

class KlantTest extends TestCase
{
    use CreatesModelTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createModelTestSchema();
    }

    public function test_klant_heeft_juiste_tabel_en_fillable(): void
    {
        $model = new Klant();

        $this->assertSame('klanten', $model->getTable());
        $this->assertContains('gezinsnaam', $model->getFillable());
        $this->assertContains('aantal_babys', $model->getFillable());
    }
}

