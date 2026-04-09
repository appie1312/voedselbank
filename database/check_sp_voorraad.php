<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::select('CALL spGetVoorraadOverzicht()');
echo 'sp_rows='.count($rows).PHP_EOL;
foreach ($rows as $r) {
    echo $r->product_naam.' | '.$r->hoeveelheid.' | '.$r->status.PHP_EOL;
}
