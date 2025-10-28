<?php

use App\Models\Market;
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
        $market_id= Market::where('name' , 'Mercadona')->first()->id;


        Schema::table('sections', function (Blueprint $table) use ($market_id) {
               $table->unsignedBigInteger('market_id')->before('image')->default($market_id); // Usa nullable si no es obligatorio
            $table->foreign('market_id')->references('id')->on('markets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
              $table->dropForeign(['market_id']);
            $table->dropColumn('market_id');
        });
    }
};
