<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking CitrineOS database for Locations...\n\n";

// Check if Locations table exists
$tables = \DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public' AND tablename ILIKE '%location%'");
echo "Tables with 'location' in name:\n";
foreach ($tables as $table) {
    echo "  - " . $table->tablename . "\n";
}

// Check for ChargingStation tables
$tables = \DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public' AND tablename ILIKE '%charging%'");
echo "\nTables with 'charging' in name:\n";
foreach ($tables as $table) {
    echo "  - " . $table->tablename . "\n";
}

// List all public tables
echo "\nAll public schema tables:\n";
$allTables = \DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public' ORDER BY tablename");
foreach ($allTables as $table) {
    echo "  - " . $table->tablename . "\n";
}
