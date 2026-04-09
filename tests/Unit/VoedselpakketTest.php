<?php

namespace Tests\Unit;

use App\Models\Voedselpakket;
use Tests\TestCase;
use Tests\Unit\Support\CreatesModelTestSchema;

class VoedselpakketTest extends TestCase
{
    use CreatesModelTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createModelTestSchema();
    }

    public function test_voedselpakket_heeft_juiste_tabel_fillable_en_casts(): void
    {
        $model = new Voedselpakket();

        $this->assertSame('voedselpakketten', $model->getTable());
        $this->assertContains('klant_id', $model->getFillable());
        $this->assertContains('datum_samenstelling', $model->getFillable());
        $this->assertContains('datum_uitgifte', $model->getFillable());

        $casts = $model->getCasts();
        $this->assertSame('date', $casts['datum_samenstelling']);
        $this->assertSame('date', $casts['datum_uitgifte']);
    }
}
