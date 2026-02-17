<?php

namespace App\Http\Controllers;

use App\Models\ChargePoint;
use App\Models\ChargingSession;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAnalyticsController extends Controller
{
    public function index()
    {
        return response()->json([
            'revenuePerStation'    => $this->revenuePerStation(),
            'chargerUtilisation'   => $this->chargerUtilisation(),
            'sessionHeatmap'       => $this->sessionHeatmap(),
            'peakUsage'            => $this->peakUsage(),
            'energyConsumption'    => $this->energyConsumption(),
            'carbonSavings'        => $this->carbonSavings(),
            'profitabilityByLocation' => $this->profitabilityByLocation(),
        ]);
    }

    /**
     * 1. Revenue per station — SUM(total_cost) grouped by charger
     */
    private function revenuePerStation(): array
    {
        $data = ChargingSession::select('charge_point_id', DB::raw('SUM(total_cost) as revenue'))
            ->where('status', 'completed')
            ->groupBy('charge_point_id')
            ->with('chargePoint')
            ->get();

        $labels = [];
        $values = [];
        foreach ($data as $row) {
            $labels[] = $row->chargePoint?->identifier ?? 'Unknown';
            $values[] = round((float) $row->revenue, 2);
        }

        return ['labels' => $labels, 'data' => $values];
    }

    /**
     * 2. Charger utilisation % — session hours / total available hours (last 7 days)
     */
    private function chargerUtilisation(): array
    {
        $since = Carbon::now()->subDays(7);
        $totalHours = 7 * 24; // hours available per charger in the period

        $chargers = ChargePoint::all();
        $labels = [];
        $values = [];

        foreach ($chargers as $cp) {
            $sessionHours = ChargingSession::where('charge_point_id', $cp->id)
                ->where('status', 'completed')
                ->where('start_time', '>=', $since)
                ->get()
                ->sum(function ($s) {
                    if (!$s->start_time || !$s->end_time) return 0;
                    return Carbon::parse($s->start_time)->diffInMinutes(Carbon::parse($s->end_time)) / 60;
                });

            $labels[] = $cp->identifier;
            $values[] = $totalHours > 0 ? round(($sessionHours / $totalHours) * 100, 1) : 0;
        }

        return ['labels' => $labels, 'data' => $values];
    }

    /**
     * 3. Charging session heatmap — sessions grouped by day-of-week × hour-of-day
     *    Returns a 7×24 matrix (Mon=0 .. Sun=6, hours 0-23)
     */
    private function sessionHeatmap(): array
    {
        // Initialize 7×24 matrix with zeros
        $matrix = array_fill(0, 7, array_fill(0, 24, 0));

        $sessions = ChargingSession::whereNotNull('start_time')->get();

        foreach ($sessions as $s) {
            $start = Carbon::parse($s->start_time);
            $dayOfWeek = ($start->dayOfWeek + 6) % 7; // Convert: Mon=0 .. Sun=6
            $hour = $start->hour;
            $matrix[$dayOfWeek][$hour]++;
        }

        return [
            'days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'hours' => range(0, 23),
            'matrix' => $matrix,
        ];
    }

    /**
     * 4. Peak usage analytics — session count per hour-of-day across all data
     */
    private function peakUsage(): array
    {
        $hourCounts = array_fill(0, 24, 0);

        $sessions = ChargingSession::whereNotNull('start_time')->get();
        foreach ($sessions as $s) {
            $hour = Carbon::parse($s->start_time)->hour;
            $hourCounts[$hour]++;
        }

        $labels = array_map(fn($h) => sprintf('%02d:00', $h), range(0, 23));

        return ['labels' => $labels, 'data' => array_values($hourCounts)];
    }

    /**
     * 5. Energy consumption report — SUM(kwh_consumed) grouped by date, last 7 days
     */
    private function energyConsumption(): array
    {
        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('D, M j');

            $kwh = ChargingSession::where('status', 'completed')
                ->whereDate('start_time', $date->toDateString())
                ->sum('kwh_consumed');

            $values[] = round((float) $kwh, 2);
        }

        return ['labels' => $labels, 'data' => $values];
    }

    /**
     * 6. Carbon savings estimation
     *    Malaysia grid emission factor ≈ 0.585 kg CO₂/kWh
     */
    private function carbonSavings(): array
    {
        $totalKwh = (float) ChargingSession::where('status', 'completed')->sum('kwh_consumed');
        $emissionFactor = 0.585; // kg CO₂ per kWh
        $carbonKg = $totalKwh * $emissionFactor;
        $treesEquivalent = round($carbonKg / 21.77); // ~21.77 kg CO₂ per tree per year

        return [
            'total_kwh'        => round($totalKwh, 2),
            'carbon_saved_kg'  => round($carbonKg, 2),
            'carbon_saved_ton' => round($carbonKg / 1000, 3),
            'trees_equivalent' => $treesEquivalent,
        ];
    }

    /**
     * 7. Profitability by location — revenue, sessions, energy, margin per location
     */
    private function profitabilityByLocation(): array
    {
        $locations = Location::with('chargePoints')->get();
        $result = [];

        foreach ($locations as $loc) {
            $cpIds = $loc->chargePoints->pluck('id');

            $sessions = ChargingSession::whereIn('charge_point_id', $cpIds)
                ->where('status', 'completed');

            $revenue = round((float) (clone $sessions)->sum('total_cost'), 2);
            $energy = round((float) (clone $sessions)->sum('kwh_consumed'), 2);
            $count = (clone $sessions)->count();

            // Estimated cost per kWh (electricity cost ≈ RM 0.37/kWh in Malaysia)
            $electricityCost = $energy * 0.37;
            $profit = $revenue - $electricityCost;
            $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

            $result[] = [
                'name'     => $loc->name,
                'revenue'  => $revenue,
                'sessions' => $count,
                'energy'   => $energy,
                'margin'   => $margin,
            ];
        }

        return $result;
    }
}
