<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VoedselpakketScenarioFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createVoedselpakketScenarioSchema();
    }

    public function test_manager_ziet_overzicht_van_alle_gezinnen_met_voedselpakketten(): void
    {
        $manager = $this->maakGebruiker(User::ROLE_DIRECTIE);

        $bergkampGezinId = $this->maakKlant('BergkampGezin');
        $zevenhuizenGezinId = $this->maakKlant('ZevenhuizenGezin');

        $this->maakVoedselpakket($bergkampGezinId);
        $this->maakVoedselpakket($zevenhuizenGezinId);

        $this->actingAs($manager)
            ->get(route('directie.voedselpakketten.index'))
            ->assertOk()
            ->assertSeeText('Overzicht Voedselpakketten')
            ->assertSeeText('BergkampGezin')
            ->assertSeeText('ZevenhuizenGezin');
    }

    public function test_manager_ziet_melding_als_eetwens_geen_varken_geen_resultaten_heeft(): void
    {
        $manager = $this->maakGebruiker(User::ROLE_DIRECTIE);

        DB::table('wens_allergies')->insert([
            ['beschrijving' => 'Geen Varken', 'created_at' => now(), 'updated_at' => now()],
            ['beschrijving' => 'Gluten', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $klantId = $this->maakKlant('Familie Zonder Varken');
        $this->koppelEetwens($klantId, 'Gluten');
        $this->maakVoedselpakket($klantId);

        $this->actingAs($manager)
            ->get(route('directie.voedselpakketten.index', ['eetwens' => 'Geen Varken']))
            ->assertOk()
            ->assertSeeText('Er zijn geen gezinnen bekent die de geselecteerde eetwens hebben');
    }

    public function test_voedselpakket_succesvol_samenstellen_en_opslaan(): void
    {
        $magazijnMedewerker = $this->maakGebruiker(User::ROLE_MAGAZIJN_MEDEWERKER);
        $klantId = $this->maakKlant('SamenstelGezin');
        $pakketId = $this->maakVoedselpakket($klantId);
        $productId = $this->maakProductMetVoorraad('Pasta', '1234567890123', 10);

        $response = $this->actingAs($magazijnMedewerker)->post(
            route('magazijn_medewerker.voedselpakketten.samenstellen.opslaan', ['id' => $pakketId]),
            ['aantallen' => [$productId => 3]]
        );

        $response
            ->assertRedirect(route('magazijn_medewerker.voedselpakketten.index'))
            ->assertSessionHas('status_success', 'Het voedselpakket is opgeslagen.');

        $this->assertDatabaseHas('pakket_product', [
            'pakket_id' => $pakketId,
            'product_id' => $productId,
            'aantal' => 3,
        ]);

        $this->assertDatabaseHas('voorraad', [
            'product_id' => $productId,
            'hoeveelheid' => 7,
        ]);
    }

    public function test_voedselpakket_samenstellen_toont_foutmelding_bij_storing(): void
    {
        $magazijnMedewerker = $this->maakGebruiker(User::ROLE_MAGAZIJN_MEDEWERKER);
        $klantId = $this->maakKlant('StoringGezin');
        $pakketId = $this->maakVoedselpakket($klantId);
        $productId = $this->maakProductMetVoorraad('Rijst', '1234567890124', 10);

        Schema::dropIfExists('pakket_product');

        $this->actingAs($magazijnMedewerker)
            ->post(route('magazijn_medewerker.voedselpakketten.samenstellen.opslaan', ['id' => $pakketId]), [
                'aantallen' => [$productId => 2],
            ])
            ->assertSessionHas('status_error', 'Het voedselpakket kon niet worden opgeslagen.');
    }

    public function test_vrijwilliger_kan_status_van_voedselpakket_wijzigen_naar_uitgereikt(): void
    {
        $vrijwilliger = $this->maakGebruiker(User::ROLE_VRIJWILLIGER);
        $klantId = $this->maakKlant('BergkampGezin', 'binnen_land');
        $pakketId = $this->maakVoedselpakket($klantId, null);

        $this->actingAs($vrijwilliger)
            ->put(route('vrijwilliger.voedselpakketten.update', ['id' => $pakketId]), [
                'status' => 'Uitgereikt',
            ])
            ->assertRedirect(route('vrijwilliger.voedselpakketten.edit', ['id' => $pakketId]))
            ->assertSessionHas('status_success', 'De wijziging is doorgevoerd')
            ->assertSessionHas('status_success_timeout', 3000)
            ->assertSessionHas('status_success_redirect', route('vrijwilliger.voedselpakketten.index'));

        $this->assertDatabaseHas('voedselpakketten', [
            'id' => $pakketId,
            'datum_uitgifte' => Carbon::now()->toDateString(),
        ]);
    }

    public function test_vrijwilliger_kan_status_niet_wijzigen_bij_niet_ingeschreven_gezin(): void
    {
        $vrijwilliger = $this->maakGebruiker(User::ROLE_VRIJWILLIGER);
        $klantId = $this->maakKlant('ZevenhuizenGezin', 'buiten_land');
        $pakketId = $this->maakVoedselpakket($klantId, null);

        $response = $this->actingAs($vrijwilliger)
            ->get(route('vrijwilliger.voedselpakketten.edit', ['id' => $pakketId]))
            ->assertOk()
            ->assertSeeText('Dit gezin is niet meer ingeschreven bij de voedselbank en daarom kan er geen voedselpakket worden uitgereikt');

        $inhoud = $response->getContent();
        $this->assertMatchesRegularExpression('/id=\"status\"[^>]*disabled/', $inhoud);
        $this->assertMatchesRegularExpression('/Wijzig status voedselpakket<\/button>/', $inhoud);
        $this->assertMatchesRegularExpression('/<button[^>]*disabled[^>]*>Wijzig status voedselpakket<\/button>/', $inhoud);

        $this->actingAs($vrijwilliger)
            ->put(route('vrijwilliger.voedselpakketten.update', ['id' => $pakketId]), [
                'status' => 'Uitgereikt',
            ])
            ->assertRedirect(route('vrijwilliger.voedselpakketten.edit', ['id' => $pakketId]))
            ->assertSessionHas('status_error', 'Dit gezin is niet meer ingeschreven bij de voedselbank en daarom kan er geen voedselpakket worden uitgereikt');

        $this->assertDatabaseHas('voedselpakketten', [
            'id' => $pakketId,
            'datum_uitgifte' => null,
        ]);
    }

    public function test_voedselpakket_verwijderen_succesvol(): void
    {
        $magazijnMedewerker = $this->maakGebruiker(User::ROLE_MAGAZIJN_MEDEWERKER);
        $klantId = $this->maakKlant('VerwijderGezin');
        $pakketId = $this->maakVoedselpakket($klantId, now()->subDay()->toDateString());

        $this->actingAs($magazijnMedewerker)
            ->delete(route('magazijn_medewerker.voedselpakketten.destroy', ['id' => $pakketId]))
            ->assertRedirect(route('magazijn_medewerker.voedselpakketten.index'))
            ->assertSessionHas('status_success', 'Het voedselpakket is verwijderd.');

        $this->assertDatabaseMissing('voedselpakketten', ['id' => $pakketId]);
    }

    public function test_voedselpakket_verwijderen_toont_foutmelding_bij_storing(): void
    {
        $magazijnMedewerker = $this->maakGebruiker(User::ROLE_MAGAZIJN_MEDEWERKER);
        $klantId = $this->maakKlant('StoringBijVerwijderen');
        $pakketId = $this->maakVoedselpakket($klantId, now()->subDay()->toDateString());

        Schema::dropIfExists('voedselpakketten');

        $this->actingAs($magazijnMedewerker)
            ->delete(route('magazijn_medewerker.voedselpakketten.destroy', ['id' => $pakketId]))
            ->assertRedirect(route('magazijn_medewerker.voedselpakketten.index'))
            ->assertSessionHas('status_error', 'Het voedselpakket kon niet worden verwijderd.');
    }

    private function maakGebruiker(string $role): User
    {
        return User::query()->create([
            'name' => 'Testgebruiker ' . $role,
            'email' => $role . '_' . uniqid('', true) . '@example.test',
            'password' => bcrypt('secret123'),
            'role' => $role,
        ]);
    }

    private function maakKlant(string $gezinsnaam, string $aanwezigheidsstatus = 'binnen_land'): int
    {
        return (int) DB::table('klanten')->insertGetId([
            'gezinsnaam' => $gezinsnaam,
            'adres' => 'Dorpsstraat 1',
            'telefoonnummer' => '0612345678',
            'emailadres' => strtolower($gezinsnaam) . '@example.test',
            'aanwezigheidsstatus' => $aanwezigheidsstatus,
            'aantal_volwassenen' => 2,
            'aantal_kinderen' => 1,
            'aantal_babys' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function maakVoedselpakket(int $klantId, ?string $datumUitgifte = null): int
    {
        return (int) DB::table('voedselpakketten')->insertGetId([
            'klant_id' => $klantId,
            'datum_samenstelling' => now()->toDateString(),
            'datum_uitgifte' => $datumUitgifte,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function koppelEetwens(int $klantId, string $beschrijving): void
    {
        $wensId = DB::table('wens_allergies')->where('beschrijving', $beschrijving)->value('id');

        DB::table('klant_wens')->insert([
            'klant_id' => $klantId,
            'wens_id' => $wensId,
        ]);
    }

    private function maakProductMetVoorraad(string $productnaam, string $eanNummer, int $voorraad): int
    {
        $categorieId = (int) DB::table('categories')->insertGetId([
            'naam' => 'Droogwaren ' . uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productId = (int) DB::table('products')->insertGetId([
            'productnaam' => $productnaam . ' ' . uniqid(),
            'ean_nummer' => $eanNummer,
            'aantal_in_voorraad' => $voorraad,
            'categorie_id' => $categorieId,
            'leverancier_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('voorraad')->insert([
            'product_id' => $productId,
            'hoeveelheid' => $voorraad,
            'minimum_voorraad' => 0,
            'locatie' => 'Schap 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $productId;
    }

    private function createVoedselpakketScenarioSchema(): void
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

        Schema::create('klanten', function (Blueprint $table): void {
            $table->id();
            $table->string('gezinsnaam', 100);
            $table->string('adres');
            $table->string('telefoonnummer', 20);
            $table->string('emailadres', 150)->nullable();
            $table->string('aanwezigheidsstatus', 30)->default('binnen_land');
            $table->integer('aantal_volwassenen')->default(0);
            $table->integer('aantal_kinderen')->default(0);
            $table->integer('aantal_babys')->default(0);
            $table->timestamps();
        });

        Schema::create('wens_allergies', function (Blueprint $table): void {
            $table->id();
            $table->string('beschrijving', 100)->unique();
            $table->timestamps();
        });

        Schema::create('klant_wens', function (Blueprint $table): void {
            $table->unsignedBigInteger('klant_id');
            $table->unsignedBigInteger('wens_id');
            $table->primary(['klant_id', 'wens_id']);
        });

        Schema::create('voedselpakketten', function (Blueprint $table): void {
            $table->id();
            $table->date('datum_samenstelling');
            $table->date('datum_uitgifte')->nullable();
            $table->unsignedBigInteger('klant_id');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('naam', 100)->unique();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('productnaam', 150)->unique();
            $table->string('ean_nummer', 13)->unique();
            $table->integer('aantal_in_voorraad')->default(0);
            $table->unsignedBigInteger('categorie_id');
            $table->unsignedBigInteger('leverancier_id')->nullable();
            $table->timestamps();
        });

        Schema::create('voorraad', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id')->unique();
            $table->integer('hoeveelheid')->default(0);
            $table->integer('minimum_voorraad')->default(0);
            $table->string('locatie', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('pakket_product', function (Blueprint $table): void {
            $table->unsignedBigInteger('pakket_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('aantal');
            $table->primary(['pakket_id', 'product_id']);
        });
    }
}
