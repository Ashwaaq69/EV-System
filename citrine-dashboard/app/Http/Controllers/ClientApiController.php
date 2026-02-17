<?php

namespace App\Http\Controllers;

use App\Models\ChargePoint;
use App\Models\FavoriteStation;
use App\Models\Feedback;
use App\Models\Reservation;
use App\Models\ChargingSession;
use App\Models\EvVehicle;
use App\Models\Wallet;
use App\Models\Notification;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\PricingPolicy;
use App\Models\PromoCode;
use App\Models\Transaction;
use App\Models\SettlementReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientApiController extends Controller
{
    /**
     * Get real notifications for the client.
     */
    public function notifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'is_read' => $n->is_read,
                    'time' => $n->created_at->diffForHumans()
                ];
            });

        return response()->json($notifications);
    }

    /**
     * Mark notifications as read.
     */
    public function readNotifications()
    {
        Notification::where('user_id', Auth::id())->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * Get all stations with their current status and favorite status for the user.
     */
    public function stations()
    {
        $user = Auth::user();
        $stations = ChargePoint::with('location')->get()->map(function ($cp) use ($user) {
            $isFavorite = $user ? FavoriteStation::where('user_id', $user->id)->where('charge_point_id', $cp->id)->exists() : false;
            
            return [
                'id' => $cp->id,
                'identifier' => $cp->identifier,
                'name' => $cp->location?->name ?? $cp->identifier,
                'address' => $cp->location?->address ?? 'N/A',
                'status' => $cp->status,
                'lat' => $cp->location?->lat ? (float) $cp->location->lat : 3.1472, // Fallback to KL center
                'lng' => $cp->location?->lng ? (float) $cp->location->lng : 101.6934,
                'distance' => round(rand(5, 100) / 10, 1), // Simulated distance
                'rating' => 4.5, // Future: calculate from Feedback
                'reviews' => Feedback::where('charge_point_id', $cp->id)->count(),
                'isFavorite' => $isFavorite
            ];
        });

        return response()->json($stations);
    }

    /**
     * Toggle a station as favorite.
     */
    public function toggleFavorite(Request $request)
    {
        $request->validate(['charge_point_id' => 'required|exists:charge_points,id']);
        $user = Auth::user();
        $id = $request->charge_point_id;

        $favorite = FavoriteStation::where('user_id', $user->id)->where('charge_point_id', $id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['isFavorite' => false, 'message' => 'Removed from favorites']);
        }

        FavoriteStation::create([
            'user_id' => $user->id,
            'charge_point_id' => $id
        ]);

        return response()->json(['isFavorite' => true, 'message' => 'Added to favorites']);
    }

    /**
     * Submit rating and feedback for a session/station.
     */
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:charging_sessions,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        $session = ChargingSession::findOrFail($request->session_id);

        Feedback::create([
            'user_id' => Auth::id(),
            'charge_point_id' => $session->charge_point_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json(['success' => true, 'message' => 'Thank you for your feedback!']);
    }

    /**
     * Get paginated charging sessions for the authenticated user with optional search.
     */
    public function sessions(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $query = ChargingSession::with(['chargePoint.location', 'vehicle'])
            ->where('user_id', $user->id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('chargePoint', function($cp) use ($search) {
                    $cp->where('identifier', 'like', "%{$search}%")
                       ->orWhereHas('location', function($loc) use ($search) {
                           $loc->where('name', 'like', "%{$search}%");
                       });
                })->orWhereHas('vehicle', function($v) use ($search) {
                    $v->where('brand', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%")
                      ->orWhere('plate_number', 'like', "%{$search}%");
                });
            });
        }

        $query->orderBy('start_time', 'desc');

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
     * Download invoice/receipt for a session.
     */
    public function downloadInvoice($id)
    {
        $session = ChargingSession::with(['chargePoint.location', 'vehicle', 'transactions'])
            ->where('user_id', Auth::id())
            ->where('id', (int)$id)
            ->firstOrFail();

        $txn = $session->transactions->first();
        
        // Find subscription to get discount percentage if applicable
        $subscription = UserSubscription::with('plan')
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        $data = [
            'session_id' => str_pad((string) $session->id, 6, '0', STR_PAD_LEFT),
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
            'discount_percentage' => $subscription?->plan->discount_percentage ?? 0,
            'tax' => (float) ($txn->tax_amount ?? 0),
            'total_rm' => (float) $session->total_cost,
            'currency' => 'RM',
            'status' => strtoupper($session->status),
            'duration' => $session->end_time ? $session->start_time->diffForHumans($session->end_time, true) : 'N/A'
        ];

        return view('receipt', $data);
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
     * Top-up wallet.
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

        // Notify user
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Wallet Top-up',
            'message' => "Successfully added RM " . number_format($request->amount, 2) . " to your wallet.",
            'type' => 'success'
        ]);

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
            'charge_point_id' => 'required|exists:charge_points,id',
            'slot_minutes' => 'integer|min:15|max:120',
        ]);

        $slotMinutes = $request->input('slot_minutes', 15);
        $cp = ChargePoint::findOrFail($request->charge_point_id);
        
        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'charge_point_id' => $cp->id,
            'start_time' => now(),
            'end_time' => now()->addMinutes($slotMinutes),
            'status' => 'active'
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Slot Reserved',
            'message' => "Reserved station {$cp->identifier} for {$slotMinutes} minutes.",
            'type' => 'info'
        ]);

        return response()->json([
            'reserved' => true,
            'reservation_id' => $reservation->id,
            'expires_at' => $reservation->end_time->toIso8601String(),
            'message' => "Slot reserved for {$slotMinutes} minutes",
        ]);
    }

    /**
     * Start a new charging session.
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'charge_point_id' => 'required|exists:charge_points,id',
            'vehicle_id' => 'required|exists:ev_vehicles,id'
        ]);

        $cp = ChargePoint::findOrFail($request->charge_point_id);
        $vehicle = EvVehicle::where('user_id', Auth::id())->findOrFail($request->vehicle_id);

        $session = ChargingSession::create([
            'user_id' => Auth::id(),
            'charge_point_id' => $cp->id,
            'vehicle_id' => $vehicle->id,
            'start_time' => now(),
            'status' => 'active',
            'kwh_consumed' => 0,
            'total_cost' => 0,
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Charging Started',
            'message' => "Your charging session for {$vehicle->brand} {$vehicle->model} ({$vehicle->plate_number}) has started at {$cp->identifier}.",
            'type' => 'success'
        ]);

        return response()->json($session->load('vehicle'));
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
            $kwh = (float) $request->input('kwh', $session->kwh_consumed);
            
            // Calculate Dynamic Price
            $policy = PricingPolicy::where('is_active', true)->first();
            $rate = 1.2; // Default
            if ($policy) {
                $nowTime = now()->format('H:i:s');
                if ($nowTime >= $policy->peak_start && $nowTime <= $policy->peak_end) {
                    $rate = $policy->peak_rate_per_kwh;
                } else {
                    $rate = $policy->base_rate_per_kwh;
                }
            }

            $subtotal = $kwh * $rate;
            
            // Apply Subscription Discount
            $discount = 0;
            $subscription = UserSubscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->first();
            
            if ($subscription) {
                $discount = $subtotal * ($subscription->plan->discount_percentage / 100);
            }

            // Apply Tax (SST 8%)
            $taxRate = 0.08;
            $tax = ($subtotal - $discount) * $taxRate;
            $totalCost = ($subtotal - $discount) + $tax;
            
            $session->update([
                'status' => 'completed',
                'end_time' => now(),
                'kwh_consumed' => $kwh,
                'total_cost' => $totalCost,
            ]);

            $userWallet = Wallet::firstOrCreate(
                ['user_id' => Auth::id()],
                ['balance' => 0, 'currency' => 'RM']
            );

            if ($userWallet->balance >= $totalCost) {
                $userWallet->decrement('balance', $totalCost);
                
                // Create Transaction Record
                Transaction::create([
                    'session_id' => $session->id,
                    'user_id' => Auth::id(),
                    'transaction_type' => 'debit',
                    'amount' => $totalCost,
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'discount_amount' => $discount,
                    'payment_method' => 'wallet',
                    'status' => 'completed',
                    'reference_id' => 'TXN-' . strtoupper(uniqid()),
                    'metadata' => [
                        'rate_applied' => $rate,
                        'is_peak' => isset($policy) && ($nowTime >= $policy->peak_start && $nowTime <= $policy->peak_end)
                    ]
                ]);
            }

            Notification::create([
                'user_id' => Auth::id(),
                'title' => 'Charging Completed',
                'message' => "Session ended. RM " . number_format($totalCost, 2) . " deducted (Includes RM ".number_format($tax, 2)." SST).",
                'type' => 'info'
            ]);
        }

        return response()->json($session);
    }

    /**
     * Billing Methods
     */
    public function billingSummary()
    {
        $user = Auth::user();
        $subscription = UserSubscription::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();
            
        $wallet = Wallet::where('user_id', $user->id)->first();
        $plans = SubscriptionPlan::all();
        
        return response()->json([
            'wallet' => $wallet,
            'active_subscription' => $subscription,
            'plans' => $plans
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:subscription_plans,id']);
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        
        $wallet = Wallet::where('user_id', Auth::id())->first();
        if (!$wallet || $wallet->balance < $plan->price) {
            return response()->json(['message' => 'Insufficient balance to subscribe.'], 400);
        }
        
        $wallet->decrement('balance', $plan->price);
        
        // Deactivate old subscriptions
        UserSubscription::where('user_id', Auth::id())->update(['status' => 'expired']);
        
        $sub = UserSubscription::create([
            'user_id' => Auth::id(),
            'subscription_plan_id' => $plan->id,
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
            'status' => 'active'
        ]);

        Transaction::create([
            'user_id' => Auth::id(),
            'transaction_type' => 'debit',
            'amount' => $plan->price,
            'subtotal' => $plan->price,
            'tax_amount' => 0,
            'payment_method' => 'wallet',
            'status' => 'completed',
            'reference_id' => 'SUB-' . strtoupper(uniqid()),
            'metadata' => ['plan_name' => $plan->name]
        ]);

        return response()->json(['success' => true, 'subscription' => $sub]);
    }

    public function applyPromo(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $promo = PromoCode::where('code', $request->code)
            ->where('expires_at', '>', now())
            ->where(function($q) {
                $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->first();
            
        if (!$promo) {
            return response()->json(['message' => 'Invalid or expired promo code.'], 400);
        }
        
        return response()->json(['isValid' => true, 'promo' => $promo]);
    }
}
