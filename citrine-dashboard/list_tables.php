<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Searching for 'Location' tables...\n";
$tables = \DB::select("SELECT schemaname, tablename FROM pg_catalog.pg_tables WHERE tablename ILIKE '%location%'");
foreach ($tables as $table) {
    echo $table->schemaname . '.' . $table->tablename . "\n";
}
