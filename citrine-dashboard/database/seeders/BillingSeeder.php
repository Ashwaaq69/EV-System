<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\PricingPolicy;
use App\Models\PromoCode;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        // Subscription Plans
        SubscriptionPlan::create([
            'name' => 'Bronze Tier',
            'price' => 50.00,
            'free_kwh' => 20,
            'discount_percentage' => 5.00,
            'duration_days' => 30,
            'description' => 'Perfect for occasional chargers. Includes 20kWh free every month.'
        ]);

        SubscriptionPlan::create([
            'name' => 'Silver Tier',
            'price' => 150.00,
            'free_kwh' => 70,
            'discount_percentage' => 10.00,
            'duration_days' => 30,
            'description' => 'Best value for daily commuters. High inclusive energy and double-digit discounts.'
        ]);

        SubscriptionPlan::create([
            'name' => 'Gold Pass',
            'price' => 300.00,
            'free_kwh' => 150,
            'discount_percentage' => 20.00,
            'duration_days' => 30,
            'description' => 'Premium access with massive energy allowance and maximum priority discounts.'
        ]);

        // Pricing Policy
        PricingPolicy::create([
            'name' => 'Standard Malaysia Tariff',
            'base_rate_per_kwh' => 1.2000,
            'peak_rate_per_kwh' => 1.5000,
            'peak_start' => '18:00:00',
            'peak_end' => '22:00:00',
            'is_active' => true
        ]);

        // Promo Codes
        PromoCode::create([
            'code' => 'CITRINE2026',
            'type' => 'percentage',
            'value' => 10.00,
            'expires_at' => now()->addYear(),
            'usage_limit' => 500,
            'min_spend' => 10.00
        ]);

        PromoCode::create([
            'code' => 'WELCOME5',
            'type' => 'fixed',
            'value' => 5.00,
            'expires_at' => now()->addYear(),
            'usage_limit' => 1000,
            'min_spend' => 5.00
        ]);
    }
}
