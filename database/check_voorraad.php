<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo 'has_voorraad_table='.(Schema::hasTable('voorraad') ? '1' : '0').PHP_EOL;
echo 'products_count='.DB::table('products')->count().PHP_EOL;
echo 'voorraad_count='.DB::table('voorraad')->count().PHP_EOL;
$rows = DB::select('CALL spGetVoorraadOverzicht()');
echo 'sp_rows='.count($rows).PHP_EOL;
