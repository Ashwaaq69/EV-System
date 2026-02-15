<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        $sessions = ChargingSession::with(['user', 'chargePoint.location'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->through(function ($session) {
                return [
                    'id' => $session->id,
                    'user' => $session->user->name,
                    'charger' => $session->chargePoint->identifier,
                    'location' => $session->chargePoint->location ? $session->chargePoint->location->name : 'N/A',
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'kwh' => $session->kwh_consumed,
                    'cost' => $session->total_cost,
                    'status' => $session->status,
                ];
            });

        return Inertia::render('Transactions', [
            'sessions' => $sessions,
        ]);
    }

    public function stopSession($id)
    {
        $session = ChargingSession::findOrFail($id);
        
        if ($session->status === 'active') {
            $session->update([
                'status' => 'completed',
                'end_time' => now(),
            ]);
        }

        return back()->with('message', 'Session stopped successfully');
    }
}
