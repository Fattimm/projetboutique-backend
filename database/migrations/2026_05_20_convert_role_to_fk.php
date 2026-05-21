<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Créer colonne temporaire pour la FK
            $table->unsignedBigInteger('role_id_new')->nullable()->after('role_id');
        });

        // Mapper les strings ENUM aux IDs de la table roles
        DB::table('users')->update([
            'role_id_new' => DB::raw("CASE role_id
                WHEN 'ADMIN' THEN 1
                WHEN 'BOUTIQUIER' THEN 2
                WHEN 'CLIENT' THEN 3
                ELSE 1
            END")
        ]);

        // Supprimer l'ancienne colonne ENUM
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });

        // Renommer la colonne temporaire
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_id_new', 'role_id');
        });

        // Ajouter la contrainte de FK
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la FK
            $table->dropForeign(['role_id']);
        });

        // Convertir back to ENUM
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_id_old')->nullable()->after('login');
        });

        DB::table('users')->update([
            'role_id_old' => DB::raw("CASE role_id
                WHEN 1 THEN 'ADMIN'
                WHEN 2 THEN 'BOUTIQUIER'
                WHEN 3 THEN 'CLIENT'
                ELSE 'ADMIN'
            END")
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_id_old', 'role_id');
        });
    }
};
