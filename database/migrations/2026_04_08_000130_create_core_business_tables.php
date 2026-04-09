<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table): void {
                $table->id();
                $table->string('naam', 100)->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('leveranciers')) {
            Schema::create('leveranciers', function (Blueprint $table): void {
                $table->id();
                $table->string('bedrijfsnaam', 150);
                $table->string('adres');
                $table->string('contactpersoon_naam', 100);
                $table->string('contactpersoon_email', 150);
                $table->string('telefoonnummer', 20);
                $table->dateTime('volgende_levering')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table): void {
                $table->id();
                $table->string('productnaam', 150)->unique();
                $table->string('ean_nummer', 13)->unique();
                $table->integer('aantal_in_voorraad')->default(0);
                $table->foreignId('categorie_id')->constrained('categories')->restrictOnDelete();
                $table->foreignId('leverancier_id')->nullable()->constrained('leveranciers')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('klanten')) {
            Schema::create('klanten', function (Blueprint $table): void {
                $table->id();
                $table->string('gezinsnaam', 100);
                $table->string('adres');
                $table->string('telefoonnummer', 20);
                $table->string('emailadres', 150)->nullable();
                $table->integer('aantal_volwassenen')->default(0);
                $table->integer('aantal_kinderen')->default(0);
                $table->integer('aantal_babys')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('wens_allergies')) {
            Schema::create('wens_allergies', function (Blueprint $table): void {
                $table->id();
                $table->string('beschrijving', 100)->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('klant_wens')) {
            Schema::create('klant_wens', function (Blueprint $table): void {
                $table->foreignId('klant_id')->constrained('klanten')->cascadeOnDelete();
                $table->foreignId('wens_id')->constrained('wens_allergies')->cascadeOnDelete();
                $table->primary(['klant_id', 'wens_id']);
            });
        }

        if (! Schema::hasTable('voedselpakketten')) {
            Schema::create('voedselpakketten', function (Blueprint $table): void {
                $table->id();
                $table->date('datum_samenstelling');
                $table->date('datum_uitgifte')->nullable();
                $table->foreignId('klant_id')->constrained('klanten')->restrictOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pakket_product')) {
            Schema::create('pakket_product', function (Blueprint $table): void {
                $table->foreignId('pakket_id')->constrained('voedselpakketten')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
                $table->integer('aantal');
                $table->primary(['pakket_id', 'product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pakket_product');
        Schema::dropIfExists('voedselpakketten');
        Schema::dropIfExists('klant_wens');
        Schema::dropIfExists('wens_allergies');
        Schema::dropIfExists('klanten');
        Schema::dropIfExists('products');
        Schema::dropIfExists('leveranciers');
        Schema::dropIfExists('categories');
    }
};
