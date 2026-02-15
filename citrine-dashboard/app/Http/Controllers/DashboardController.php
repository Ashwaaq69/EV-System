<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\ChargePoint;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        $chargePointsPaginator = ChargePoint::with('location')->paginate($perPage);

        $chargers = $chargePointsPaginator->through(function ($cp) {
            $isOnline = in_array($cp->status, ['Available', 'Charging']);
            
            return [
                'id'       => $cp->identifier,
                'name'     => $cp->identifier,
                'location' => $cp->location ? $cp->location->name : 'N/A',
                'online'   => $isOnline,
            ];
        });

        $totalCount = ChargePoint::count();
        $onlineCount = ChargePoint::whereIn('status', ['Available', 'Charging'])->count();
        $offlineCount = $totalCount - $onlineCount;

        return Inertia::render('Dashboard', [
            'chargers'       => $chargers,
            'onlineChargers' => $onlineCount,
            'offlineChargers'=> $offlineCount,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string|unique:charge_points,identifier|max:255',
            'location_name' => 'required|string|max:255',
        ]);

        // Create or find location
        $location = \App\Models\Location::firstOrCreate(['name' => $validated['location_name']]);

        // Create ChargePoint
        \App\Models\ChargePoint::create([
            'identifier' => $validated['identifier'],
            'location_id' => $location->id,
            'status' => 'Available', // Default status
        ]);

        return redirect()->back()->with('success', 'Charger added successfully.');
    }
}
