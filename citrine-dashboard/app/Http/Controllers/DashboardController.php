<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\ChargePoint;
use App\Models\ChargingSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        
        $query = ChargePoint::query()->with('location');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('identifier', 'like', "%{$search}%")
                  ->orWhereHas('location', function ($l) use ($search) {
                      $l->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $chargePointsPaginator = $query->paginate($perPage)->withQueryString();

        $chargers = $chargePointsPaginator->through(function ($cp) {
            $isOnline = in_array($cp->status, ['Available', 'Charging']);
            
            return [
                'id'       => $cp->id, // Use database ID for management
                'identifier' => $cp->identifier,
                'name'     => $cp->identifier,
                'location' => $cp->location ? $cp->location->name : 'N/A',
                'online'   => $isOnline,
                'status'   => $cp->status,
            ];
        });

        $totalCount = ChargePoint::count();
        $availableCount = ChargePoint::where('status', 'Available')->count();
        $chargingCount = ChargePoint::where('status', 'Charging')->count();
        $onlineCount = $availableCount + $chargingCount;
        $offlineCount = $totalCount - $onlineCount;

        $activeSessions = ChargingSession::where('status', 'active')
            ->with(['user', 'chargePoint.location'])
            ->orderBy('start_time', 'desc')
            ->get()
            ->map(fn($s) => [
                'id'         => $s->id,
                'user_name'  => $s->user?->name ?? 'Unknown',
                'user_email' => $s->user?->email ?? '',
                'charger'    => $s->chargePoint?->identifier ?? 'N/A',
                'location'   => $s->chargePoint?->location?->name ?? 'N/A',
                'start_time' => $s->start_time,
                'kwh'        => (float) $s->kwh_consumed,
                'cost'       => (float) $s->total_cost,
            ]);

        return Inertia::render('Dashboard', [
            'chargers'           => $chargers,
            'availableChargers'  => $availableCount,
            'chargingChargers'   => $chargingCount,
            'onlineChargers'     => $onlineCount,
            'offlineChargers'    => $offlineCount,
            'activeSessions'     => $activeSessions,
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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'identifier' => 'required|string|max:255|unique:charge_points,identifier,' . $id,
            'location_name' => 'required|string|max:255',
        ]);

        $cp = ChargePoint::findOrFail($id);
        
        $location = \App\Models\Location::firstOrCreate(['name' => $validated['location_name']]);

        $cp->update([
            'identifier' => $validated['identifier'],
            'location_id' => $location->id,
        ]);

        return redirect()->back()->with('success', 'Charger updated successfully.');
    }

    public function destroy($id)
    {
        $cp = ChargePoint::findOrFail($id);
        $cp->delete();

        return redirect()->back()->with('success', 'Charger deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $cp = ChargePoint::findOrFail($id);
        
        // Simple toggle between Available and Unavailable
        $newStatus = ($cp->status === 'Unavailable') ? 'Available' : 'Unavailable';
        
        $cp->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Charger status updated.');
    }
}
