<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = DB::table('users')->first()->id;
        $chargePointId = DB::table('charge_points')->first()->id;

        // Sample Charging Sessions
        $sessions = [
            [
                'user_id' => $userId,
                'charge_point_id' => $chargePointId,
                'start_time' => Carbon::now()->subHours(2),
                'end_time' => Carbon::now()->subHours(1),
                'kwh_consumed' => 15.5,
                'total_cost' => 12.40,
                'status' => 'completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $userId,
                'charge_point_id' => $chargePointId,
                'start_time' => Carbon::now()->subMinutes(30),
                'end_time' => null,
                'kwh_consumed' => 4.2,
                'total_cost' => 3.36,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $userId,
                'charge_point_id' => $chargePointId,
                'start_time' => Carbon::now()->subDays(1),
                'end_time' => Carbon::now()->subDays(1)->addHours(3),
                'kwh_consumed' => 45.0,
                'total_cost' => 36.00,
                'status' => 'completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($sessions as $sessionData) {
            $sessionId = DB::table('charging_sessions')->insertGetId($sessionData);

            // Create a corresponding transaction record
            DB::table('transactions')->insert([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'amount' => $sessionData['total_cost'],
                'payment_method' => 'Wallet',
                'status' => 'Completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create some meter values for the session
            DB::table('meter_values')->insert([
                [
                    'session_id' => $sessionId,
                    'charge_point_id' => $chargePointId,
                    'value' => $sessionData['kwh_consumed'] * 0.5,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'session_id' => $sessionId,
                    'charge_point_id' => $chargePointId,
                    'value' => $sessionData['kwh_consumed'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);
        }
    }
}
