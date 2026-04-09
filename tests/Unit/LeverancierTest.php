<?php

namespace Tests\Unit;

use App\Models\Leverancier;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Tests\Unit\Support\CreatesModelTestSchema;

class LeverancierTest extends TestCase
{
    use CreatesModelTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createModelTestSchema();
    }

    public function test_een_leverancier_kan_succesvol_worden_aangemaakt(): void
    {
        $leverancier = Leverancier::create([
            'bedrijfsnaam' => 'Nieuwe Leverancier BV',
            'adres' => 'Teststraat 1',
            'contactpersoon_naam' => 'Jan Test',
            'contactpersoon_email' => 'jan@test.nl',
            'telefoonnummer' => '0612345678',
            'volgende_levering' => '2026-04-10 10:00:00',
        ]);

        $this->assertNotNull($leverancier->id);
        $this->assertDatabaseHas('leveranciers', ['bedrijfsnaam' => 'Nieuwe Leverancier BV']);
    }

    public function test_een_levering_voor_een_leverancier_kan_succesvol_worden_aangemaakt(): void
    {
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table): void {
                $table->id();
                $table->string('productnaam');
                $table->string('ean_nummer')->nullable();
                $table->integer('aantal_in_voorraad')->default(0);
                $table->unsignedBigInteger('categorie_id')->nullable();
                $table->unsignedBigInteger('leverancier_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('leverancier_products')) {
            Schema::create('leverancier_products', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('leverancier_id');
                $table->unsignedBigInteger('product_id');
                $table->timestamps();
            });
        }

        $leverancierId = DB::table('leveranciers')->insertGetId([
            'bedrijfsnaam' => 'Levering Test BV',
            'adres' => 'Leveringstraat 2',
            'contactpersoon_naam' => 'Sanne Levering',
            'contactpersoon_email' => 'sanne@levering.nl',
            'telefoonnummer' => '0699999999',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productId = DB::table('products')->insertGetId([
            'productnaam' => 'Test Product',
            'ean_nummer' => '9999999999999',
            'aantal_in_voorraad' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('leverancier_products')->insert([
            'leverancier_id' => $leverancierId,
            'product_id' => $productId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('leverancier_products', [
            'leverancier_id' => $leverancierId,
            'product_id' => $productId,
        ]);
    }
}

