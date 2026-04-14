<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$items = [
    ['ean' => '8710400000001', 'h' => 20, 'm' => 10, 'l' => 'Stelling A'],
    ['ean' => '8710400000002', 'h' => 5, 'm' => 10, 'l' => 'Koeling 1'],
    ['ean' => '8710400000003', 'h' => 30, 'm' => 8, 'l' => 'Zuivel rek'],
    ['ean' => '8710400000004', 'h' => 12, 'm' => 6, 'l' => 'Broodrek'],
];

foreach ($items as $i) {
    $productId = DB::table('products')->where('ean_nummer', $i['ean'])->value('id');
    echo 'ean='.$i['ean'].' product_id='.($productId ?? 'null').PHP_EOL;
    if (! $productId) continue;
    DB::table('voorraad')->updateOrInsert(
        ['product_id' => $productId],
        ['hoeveelheid' => $i['h'], 'minimum_voorraad' => $i['m'], 'locatie' => $i['l'], 'created_at' => now(), 'updated_at' => now()]
    );
}

echo 'voorraad_count='.DB::table('voorraad')->count().PHP_EOL;
