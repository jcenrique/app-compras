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
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);

            $table->foreignIdFor(\App\Models\Category::class)
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Market::class)
                ->constrained()
                ->onDelete('cascade');
            $table->string('image')->nullable();
            $table->boolean('active')->default(true);
            $table->string('brand')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
