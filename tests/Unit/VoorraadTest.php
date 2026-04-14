<?php

namespace Tests\Unit;

use App\Models\VoorraadModel;
use PDO;
use Tests\TestCase;

class VoorraadTest extends TestCase
{
    public function test_voorraad_model_geeft_false_bij_fout(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $model = new VoorraadModel($pdo);

        $this->assertFalse($model->getVoorraadLijst());
    }
}

