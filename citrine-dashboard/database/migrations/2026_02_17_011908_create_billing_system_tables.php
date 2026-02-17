<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subscription Plans
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('free_kwh')->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('duration_days')->default(30);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // User Subscriptions
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->dateTime('starts_at');
            $table->dateTime('expires_at');
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->timestamps();
        });

        // Pricing Policies (Dynamic Pricing)
        Schema::create('pricing_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('base_rate_per_kwh', 10, 4);
            $table->decimal('peak_rate_per_kwh', 10, 4);
            $table->time('peak_start')->default('18:00:00');
            $table->time('peak_end')->default('22:00:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Promo Codes
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->dateTime('expires_at')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->decimal('min_spend', 10, 2)->default(0);
            $table->timestamps();
        });

        // Update Transactions Table
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transaction_type')->default('debit')->after('user_id'); // credit (top-up), debit (charging)
            $table->decimal('subtotal', 10, 2)->default(0)->after('amount');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
            $table->string('reference_id')->nullable()->after('status'); // External ref or internal invoice num
            $table->text('metadata')->nullable()->after('reference_id');
        });

        // Settlement Reports
        Schema::create('settlement_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->decimal('total_revenue', 15, 2);
            $table->decimal('total_tax', 15, 2);
            $table->decimal('total_discounts', 15, 2);
            $table->decimal('net_settlement', 15, 2);
            $table->integer('total_sessions');
            $table->string('status')->default('generated');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlement_reports');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('pricing_policies');
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('subscription_plans');
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_type', 'subtotal', 'tax_amount', 'discount_amount', 'reference_id', 'metadata']);
        });
    }
};
