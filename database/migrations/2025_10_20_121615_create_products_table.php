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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('ingredients')->nullable(); // oder JSON
            $table->longText('allergens')->nullable();  // oder JSON
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(7.00); // z.B. 7% für Lebensmittel
            $table->boolean('is_published')->default(false);
            $table->string('visibility_status')->default('draft'); // draft|active|unavailable|seasonal
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->unsignedSmallInteger('min_lead_days')->default(0);
            $table->boolean('notes_required')->default(false); // z.B. Widmungstext
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['category_id', 'is_published', 'visibility_status']);
            $table->index(['available_from', 'available_until']);
        });

        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');                 // z.B. "Frucht", "Farbe", "Größe"
            $table->string('type')->default('select'); // select|radio|multi|text
            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')->constrained()->cascadeOnDelete();
            $table->string('value');                // z.B. "Erdbeere", "26 cm"
            $table->decimal('price_delta', 10, 2)->default(0); // +/- Aufpreis
            $table->unsignedSmallInteger('extra_lead_days')->default(0);
            $table->unsignedSmallInteger('extra_lead_hours')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['product_option_id', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('products');
    }
};
