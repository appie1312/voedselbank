<?php

namespace Tests\Unit\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreatesModelTestSchema
{
    protected function createModelTestSchema(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('telefoon')->nullable();
            $table->string('adres')->nullable();
            $table->string('afdeling')->nullable();
            $table->string('beschikbaarheid')->nullable();
            $table->string('verantwoordelijkheden')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

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
}

