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
        $session = ChargingSession::with(['user', 'chargePoint.location', 'vehicle', 'transactions'])
            ->where('id', (int)$id)
            ->firstOrFail();

        $txn = $session->transactions->first();

        $data = [
            'session_id' => str_pad((string) $session->id, 6, '0', STR_PAD_LEFT),
            'customer' => $session->user?->name ?? 'Unknown',
            'date' => $session->start_time ? $session->start_time->format('M d, Y') : 'N/A',
            'time' => $session->start_time ? $session->start_time->format('H:i') : 'N/A',
            'location' => $session->chargePoint?->location?->name ?? 'N/A',
            'address' => $session->chargePoint?->location?->address ?? '',
            'charger' => $session->chargePoint?->identifier ?? 'N/A',
            'vehicle_name' => $session->vehicle ? ($session->vehicle->brand . ' ' . $session->vehicle->model) : 'Unknown Vehicle',
            'vehicle_plate' => $session->vehicle?->plate_number ?? 'N/A',
            'energy_kwh' => (float) $session->kwh_consumed,
            'subtotal' => (float) ($txn->subtotal ?? $session->total_cost),
            'discount' => (float) ($txn->discount_amount ?? 0),
            'discount_percentage' => 0, // Admin view doesn't necessarily need sub calculation
            'tax' => (float) ($txn->tax_amount ?? 0),
            'total_rm' => (float) $session->total_cost,
            'currency' => 'RM',
            'status' => strtoupper($session->status),
            'duration' => $session->end_time ? $session->start_time->diffForHumans($session->end_time, true) : 'N/A'
        ];

        return view('receipt', $data);
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
