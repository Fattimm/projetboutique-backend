<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableForSoftDeletes extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajouter la colonne deleted_at
            $table->timestamp('deleted_at')->nullable()->after('updated_at');
            
            // Supprimer la colonne deletedAt si elle existe
            if (Schema::hasColumn('users', 'deletedAt')) {
                $table->dropColumn('deletedAt');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Annuler les changements
            // Ajouter la colonne deletedAt
            $table->timestamp('deletedAt')->nullable()->after('updated_at');
            
            // Supprimer la colonne deleted_at si elle existe
            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
}
