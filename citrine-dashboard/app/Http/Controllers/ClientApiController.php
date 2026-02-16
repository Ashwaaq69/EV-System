<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use App\Models\EvVehicle;
use App\Models\Wallet;
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

        $query = ChargingSession::with('chargePoint.location')
            ->where('user_id', $user->id)
            ->orderBy('start_time', 'desc');

        $perPage = $request->input('per_page', 5);
        $sessionsPaginator = $query->paginate($perPage);

        return response()->json($sessionsPaginator);
    }

    /**
     * Get vehicles for the authenticated user.
     */
    public function vehicles()
    {
        $vehicles = EvVehicle::where('user_id', Auth::id())
            ->orderByRaw('is_default DESC')
            ->orderBy('created_at')
            ->get();

        return response()->json($vehicles);
    }

    /**
     * Store a new vehicle.
     */
    public function storeVehicle(Request $request)
    {
        $request->validate([
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'plate_number' => 'required|string|max:20',
            'battery_capacity_kwh' => 'nullable|numeric|min:0|max:500',
            'connector_type' => 'nullable|string|max:50',
            'is_default' => 'boolean',
        ]);

        $isDefault = $request->boolean('is_default');
        if ($isDefault) {
            EvVehicle::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $vehicle = EvVehicle::create([
            'user_id' => Auth::id(),
            'brand' => $request->brand,
            'model' => $request->model,
            'plate_number' => $request->plate_number,
            'battery_capacity_kwh' => $request->battery_capacity_kwh,
            'connector_type' => $request->connector_type ?? 'Type 2 / CCS',
            'is_default' => $isDefault || EvVehicle::where('user_id', Auth::id())->count() === 0,
        ]);

        return response()->json($vehicle);
    }

    /**
     * Set default vehicle.
     */
    public function setDefaultVehicle($id)
    {
        $vehicle = EvVehicle::where('user_id', Auth::id())->findOrFail($id);
        EvVehicle::where('user_id', Auth::id())->update(['is_default' => false]);
        $vehicle->update(['is_default' => true]);

        return response()->json($vehicle);
    }

    /**
     * Delete a vehicle.
     */
    public function destroyVehicle($id)
    {
        $vehicle = EvVehicle::where('user_id', Auth::id())->findOrFail($id);
        $vehicle->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Download invoice/receipt for a session (PDF or JSON for client-side PDF).
     */
    public function downloadInvoice($id)
    {
        $session = ChargingSession::with('chargePoint.location')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $data = [
            'session_id' => str_pad((string) $session->id, 6, '0', STR_PAD_LEFT),
            'date' => $session->start_time?->format('M d, Y'),
            'time' => $session->start_time?->format('H:i'),
            'location' => $session->chargePoint?->identifier ?? 'N/A',
            'energy_kwh' => (float) $session->kwh_consumed,
            'cost_rm' => (float) $session->total_cost,
            'status' => $session->status,
        ];

        return response()->streamDownload(
            function () use ($data) {
                echo json_encode($data, JSON_PRETTY_PRINT);
            },
            'invoice-' . $data['session_id'] . '.json',
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Get wallet balance.
     */
    public function wallet()
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['balance' => 0, 'currency' => 'RM']
        );

        return response()->json([
            'balance' => (float) $wallet->balance,
            'currency' => $wallet->currency,
        ]);
    }

    /**
     * Top-up wallet (placeholder: no real payment gateway).
     */
    public function topUpWallet(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:5000',
        ]);

        $wallet = Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['balance' => 0, 'currency' => 'RM']
        );

        $wallet->increment('balance', $request->amount);

        return response()->json([
            'balance' => (float) $wallet->fresh()->balance,
            'message' => 'Top-up successful',
        ]);
    }

    /**
     * Create a reservation for a charger slot.
     */
    public function createReservation(Request $request)
    {
        $request->validate([
            'station_id' => 'required|integer',
            'slot_minutes' => 'integer|min:15|max:120',
        ]);

        $slotMinutes = $request->input('slot_minutes', 15);
        $expiresAt = now()->addMinutes($slotMinutes);

        return response()->json([
            'reserved' => true,
            'station_id' => $request->station_id,
            'expires_at' => $expiresAt->toIso8601String(),
            'message' => "Slot reserved for {$slotMinutes} minutes",
        ]);
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
            $kwh = $request->input('kwh');
            $cost = $request->input('cost');
            $totalCost = is_numeric($cost) ? (float) $cost : ($session->total_cost > 0 ? (float) $session->total_cost : 0);
            $session->update([
                'status' => 'completed',
                'end_time' => now(),
                'kwh_consumed' => is_numeric($kwh) ? (float) $kwh : ($session->kwh_consumed > 0 ? $session->kwh_consumed : 0),
                'total_cost' => $totalCost,
            ]);
            $userWallet = Wallet::firstOrCreate(
                ['user_id' => Auth::id()],
                ['balance' => 0, 'currency' => 'RM']
            );
            if ($userWallet->balance >= $totalCost) {
                $userWallet->decrement('balance', $totalCost);
            }
        }

        return response()->json($session);
    }
}
