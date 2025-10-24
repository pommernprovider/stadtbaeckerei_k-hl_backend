<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->ulid('id')->primary();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending');

            $table->foreignId('branch_id')->constrained()->restrictOnDelete();

            $table->dateTime('pickup_at');

            $table->dateTime('pickup_end_at')->nullable();
            $table->string('pickup_window_label')->nullable();

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('customer_adress');
            $table->string('customer_tax');
            $table->string('customer_city');
            $table->text('customer_note')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_total', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->char('currency', 3)->default('EUR');
            $table->string('locale', 10)->nullable();

            $table->uuid('confirmation_token')->unique();
            $table->dateTime('confirmed_at')->nullable();

            $table->timestamps();


            $table->index(['branch_id', 'pickup_at']);
            $table->index(['status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('order_id')->constrained('orders')->cascadeOnDelete();

            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name_snapshot');
            $table->string('variant_name_snapshot')->nullable();

            $table->decimal('price_snapshot', 10, 2);
            $table->decimal('tax_rate_snapshot', 5, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('line_total', 10, 2);

            $table->timestamps();
        });

        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();

            $table->string('option_name_snapshot');
            $table->string('value_label_snapshot')->nullable();
            $table->text('free_text')->nullable();
            $table->decimal('price_delta_snapshot', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('order_events', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('type');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_events');
        Schema::dropIfExists('order_item_options');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
