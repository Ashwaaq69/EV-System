<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\ChargePoint;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            ['name' => 'Suria KLCC P2', 'address' => 'Level P2, KLCC', 'lat' => 3.1579, 'lng' => 101.7116],
            ['name' => 'Pavilion Bukit Jalil', 'address' => 'B1, Pillar C12', 'lat' => 3.0634, 'lng' => 101.6382],
            ['name' => 'Mid Valley Megamall', 'address' => 'P1, Zone Red', 'lat' => 3.1177, 'lng' => 101.6780],
            ['name' => 'IOI City Mall', 'address' => 'LG, Pillar B5', 'lat' => 3.0486, 'lng' => 101.6172],
            ['name' => 'Paradigm Mall', 'address' => 'B2, Near Entrance', 'lat' => 3.1076, 'lng' => 101.6055],
            ['name' => 'Sunway Pyramid', 'address' => 'B1, Blue Zone', 'lat' => 3.0731, 'lng' => 101.6077],
            ['name' => 'One Utama', 'address' => 'LG, New Wing', 'lat' => 3.1502, 'lng' => 101.6159],
            ['name' => 'The Gardens Mall', 'address' => 'P2, Zone B', 'lat' => 3.1187, 'lng' => 101.6761],
            ['name' => 'Bangsar Village', 'address' => 'G Floor, Valet Area', 'lat' => 3.1305, 'lng' => 101.6672],
            ['name' => 'Publika', 'address' => 'B2, Red Zone', 'lat' => 3.1709, 'lng' => 101.6655],
        ];

        foreach ($stations as $data) {
            $location = Location::updateOrCreate(
                ['name' => $data['name']],
                [
                    'address' => $data['address'],
                    'lat' => $data['lat'],
                    'lng' => $data['lng'],
                ]
            );

            ChargePoint::updateOrCreate(
                ['identifier' => 'CP-' . strtoupper(substr(md5($data['name']), 0, 6))],
                [
                    'location_id' => $location->id,
                    'status' => rand(0, 10) > 2 ? 'Available' : 'Charging',
                ]
            );
        }
    }
}
