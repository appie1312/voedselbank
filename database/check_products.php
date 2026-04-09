<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('products')->select('id','productnaam','ean_nummer')->orderBy('id')->get();
foreach ($rows as $r) {
    echo $r->id.' | '.$r->productnaam.' | '.$r->ean_nummer.PHP_EOL;
}
