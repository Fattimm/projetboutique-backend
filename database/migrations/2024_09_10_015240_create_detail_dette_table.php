<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
    public function up()
    {
        Schema::create('detail_dette', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dette_id'); // Référence à la dette
            $table->unsignedBigInteger('article_id'); // Référence à l'article
            $table->integer('qteVente'); // Quantité vendue
            $table->decimal('prixVente', 10, 2); // Prix de vente
            $table->timestamps();

            // Clés étrangères
            $table->foreign('dette_id')->references('id')->on('dettes')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_dette');
    }
};
