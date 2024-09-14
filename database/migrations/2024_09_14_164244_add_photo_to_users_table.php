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
            // Modifier la colonne 'email' pour qu'elle ne soit plus nullable
            $table->string('email')->nullable(false)->change();

            // Modifier la colonne 'photo' pour qu'elle soit nullable
            $table->string('photo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rendre les colonnes 'email' à nouveau nullable
            $table->string('email')->nullable()->change();

            // Rendre la colonne 'photo' non nullable
            $table->string('photo')->nullable(false)->change();
        });
    }
};
