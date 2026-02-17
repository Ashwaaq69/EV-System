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
        $search = $request->input('search');
        
        $query = ChargingSession::with(['user', 'chargePoint.location'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })->orWhereHas('chargePoint', function ($cp) use ($search) {
                    $cp->where('identifier', 'like', "%{$search}%")
                      ->orWhereHas('location', function ($l) use ($search) {
                          $l->where('name', 'like', "%{$search}%");
                      });
                });
            });
        }

        $sessions = $query->paginate($perPage)->withQueryString()
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

    public function downloadInvoice($id)
    {
        $session = ChargingSession::with(['user', 'chargePoint.location'])
            ->where('id', (int)$id)
            ->firstOrFail();

        $data = [
            'type' => 'RECEIPT',
            'session_id' => str_pad((string) $session->id, 6, '0', STR_PAD_LEFT),
            'customer' => $session->user?->name ?? 'Unknown',
            'date' => $session->start_time ? $session->start_time->format('M d, Y') : 'N/A',
            'time' => $session->start_time ? $session->start_time->format('H:i') : 'N/A',
            'location' => $session->chargePoint?->location?->name ?? 'N/A',
            'charger' => $session->chargePoint?->identifier ?? 'N/A',
            'energy_kwh' => (float) $session->kwh_consumed,
            'total_rm' => (float) $session->total_cost,
            'status' => strtoupper($session->status)
        ];

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="admin-receipt-' . $data['session_id'] . '.json"');
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
