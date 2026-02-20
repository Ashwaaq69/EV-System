<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::dropIfExists('charging_sessions');

try {
    Schema::create('charging_sessions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('charge_point_id')->constrained()->onDelete('cascade');
        $table->timestamp('start_time')->useCurrent();
        $table->timestamp('end_time')->nullable();
        $table->decimal('kwh_consumed', 10, 2)->default(0.00);
        $table->decimal('total_cost', 10, 2)->default(0.00);
        $table->string('status')->default('active');
        $table->timestamps();
    });
    echo "Success!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
