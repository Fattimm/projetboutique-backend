<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDettesTable extends Migration
{
    public function up()
    {
        Schema::create('dettes', function (Blueprint $table) {
            $table->id(); // ID unique pour chaque dette
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Clé étrangère vers la table clients
            $table->date('date'); // Date de la dette
            $table->decimal('montant', 10, 2); // Montant total de la dette
            $table->decimal('montantDu', 10, 2); // Montant dû jusqu'à présent
            $table->decimal('montantRestant', 10, 2); // Montant restant à payer
            $table->timestamps(); // Créé et mis à jour automatiquement
        });
    }

    public function down()
    {
        Schema::dropIfExists('dettes');
    }
}
