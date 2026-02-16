<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientApiController extends Controller
{
    /**
     * Get paginated charging sessions for the authenticated user.
     */
    public function sessions(Request $request)
    {
        $user = Auth::user();
        
        // If no user (e.g. standalone mode without session), return some mock data
        // but normally this will be protected by auth middleware
        
        $query = ChargingSession::with('chargePoint.location')
            ->where('user_id', $user->id)
            ->orderBy('start_time', 'desc');

        $perPage = $request->input('per_page', 5);
        return response()->json($sessionsPaginator);
    }

    /**
     * Start a new charging session.
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'charge_point_id' => 'required|exists:charge_points,id'
        ]);

        $session = ChargingSession::create([
            'user_id' => Auth::id(),
            'charge_point_id' => $request->charge_point_id,
            'start_time' => now(),
            'status' => 'active',
            'kwh_consumed' => 0,
            'total_cost' => 0,
        ]);

        return response()->json($session);
    }

    /**
     * Stop an active charging session.
     */
    public function stopSession(Request $request, $id)
    {
        $session = ChargingSession::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if ($session->status === 'active') {
            $session->update([
                'status' => 'completed',
                'end_time' => now(),
                // For demo purposes, we'll set some random consumption if it was 0
                'kwh_consumed' => $session->kwh_consumed > 0 ? $session->kwh_consumed : rand(5, 25),
                'total_cost' => $session->total_cost > 0 ? $session->total_cost : rand(10, 40),
            ]);
        }

        return response()->json($session);
    }
}
