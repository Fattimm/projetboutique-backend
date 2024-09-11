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
       Schema::table('users', function (Blueprint $table) {
            // Ajouter les colonnes email et photo comme nullable pour éviter les erreurs
            $table->string('email')->unique()->nullable()->after('login');
            $table->string('photo')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les colonnes email et photo
            $table->dropColumn('email');
            $table->dropColumn('photo');
        });
    }
};
