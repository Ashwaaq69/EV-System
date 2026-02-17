<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ChargingSession;
use App\Models\SettlementReport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function index()
    {
        $reports = SettlementReport::orderBy('report_date', 'desc')->paginate(10);
        
        $stats = [
            'total_revenue' => (float) Transaction::where('transaction_type', 'debit')->where('status', 'completed')->sum('amount'),
            'total_tax' => (float) Transaction::sum('tax_amount'),
            'active_subscribers' => \App\Models\UserSubscription::where('status', 'active')->where('expires_at', '>', now())->count(),
        ];

        return Inertia::render('Admin/Billing', [
            'reports' => $reports,
            'stats' => $stats
        ]);
    }

    public function generateReport(Request $request)
    {
        $date = $request->input('date', now()->subDay()->format('Y-m-d'));
        
        $revenue = Transaction::whereDate('created_at', $date)
            ->where('transaction_type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');
            
        $tax = Transaction::whereDate('created_at', $date)->sum('tax_amount');
        $discounts = Transaction::whereDate('created_at', $date)->sum('discount_amount');
        $sessions = ChargingSession::whereDate('created_at', $date)->count();
        
        $report = SettlementReport::create([
            'report_date' => $date,
            'total_revenue' => $revenue,
            'total_tax' => $tax,
            'total_discounts' => $discounts,
            'net_settlement' => $revenue - $tax,
            'total_sessions' => $sessions,
            'status' => 'settled'
        ]);

        return back()->with('success', 'Report generated for ' . $date);
    }
}
