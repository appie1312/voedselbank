<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo 'has_voorraad_table='.(Schema::hasTable('voorraad') ? '1' : '0').PHP_EOL;
if (Schema::hasTable('products')) echo 'products_count='.DB::table('products')->count().PHP_EOL;
if (Schema::hasTable('voorraad')) echo 'voorraad_count='.DB::table('voorraad')->count().PHP_EOL;
$proc = DB::select("SHOW PROCEDURE STATUS WHERE Db = DATABASE() AND Name = 'spGetVoorraadOverzicht'");
echo 'has_sp='.(count($proc) > 0 ? '1' : '0').PHP_EOL;
