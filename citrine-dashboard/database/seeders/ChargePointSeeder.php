<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChargePointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test location first
        $locationId = DB::table('locations')->insertGetId([
            'name' => 'Kuala Lumpur HQ',
            'address' => 'Jalan Sultan Ismail, 50250 Kuala Lumpur',
            'margin_target' => 35.00,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('charge_points')->insert([
            [
                'identifier' => 'CS-001',
                'status' => 'Available',
                'location_id' => $locationId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'identifier' => 'CS-002',
                'status' => 'Charging',
                'location_id' => $locationId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'identifier' => 'CS-003',
                'status' => 'Faulted',
                'location_id' => $locationId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'identifier' => 'CS-004',
                'status' => 'Offline',
                'location_id' => $locationId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'identifier' => 'CS-005',
                'status' => 'Available',
                'location_id' => $locationId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
