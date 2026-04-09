<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Determine the exact SQL type used by the `id` column on an existing table.
     *
     * @return array{type: string, unsigned: bool}
     */
    private function resolveIdColumnType(string $table): array
    {
        if (! Schema::hasTable($table)) {
            return ['type' => 'bigint', 'unsigned' => true];
        }

        $column = DB::table('information_schema.columns')
            ->selectRaw('DATA_TYPE as data_type, COLUMN_TYPE as column_type')
            ->whereRaw('table_schema = database()')
            ->where('table_name', $table)
            ->where('column_name', 'id')
            ->first();

        if (! $column) {
            return ['type' => 'bigint', 'unsigned' => true];
        }

        return [
            'type' => strtolower((string) $column->data_type),
            'unsigned' => str_contains(strtolower((string) $column->column_type), 'unsigned'),
        ];
    }

    /**
     * Add a foreign key column that matches the referenced table's `id` type.
     */
    private function addCompatibleForeignKey(
        Blueprint $table,
        string $column,
        string $referencesTable,
        string $onDelete = 'restrict',
        bool $nullable = false
    ): void {
        $idType = $this->resolveIdColumnType($referencesTable);

        if ($idType['type'] === 'int') {
            $definition = $idType['unsigned']
                ? $table->unsignedInteger($column)
                : $table->integer($column);
        } else {
            $definition = $idType['unsigned']
                ? $table->unsignedBigInteger($column)
                : $table->bigInteger($column);
        }

        if ($nullable) {
            $definition->nullable();
        }

        $foreign = $table->foreign($column)->references('id')->on($referencesTable);

        if ($onDelete === 'cascade') {
            $foreign->cascadeOnDelete();
            return;
        }

        if ($onDelete === 'set null') {
            $foreign->nullOnDelete();
            return;
        }

        $foreign->restrictOnDelete();
    }

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
                $this->addCompatibleForeignKey($table, 'categorie_id', 'categories', 'restrict');
                $this->addCompatibleForeignKey($table, 'leverancier_id', 'leveranciers', 'set null', true);
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
                $this->addCompatibleForeignKey($table, 'klant_id', 'klanten', 'cascade');
                $this->addCompatibleForeignKey($table, 'wens_id', 'wens_allergies', 'cascade');
                $table->primary(['klant_id', 'wens_id']);
            });
        }

        if (! Schema::hasTable('voedselpakketten')) {
            Schema::create('voedselpakketten', function (Blueprint $table): void {
                $table->id();
                $table->date('datum_samenstelling');
                $table->date('datum_uitgifte')->nullable();
                $this->addCompatibleForeignKey($table, 'klant_id', 'klanten', 'restrict');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pakket_product')) {
            Schema::create('pakket_product', function (Blueprint $table): void {
                $this->addCompatibleForeignKey($table, 'pakket_id', 'voedselpakketten', 'cascade');
                $this->addCompatibleForeignKey($table, 'product_id', 'products', 'restrict');
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
