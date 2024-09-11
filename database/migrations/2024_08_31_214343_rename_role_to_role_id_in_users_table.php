<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameRoleToRoleIdInUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Renommer la colonne 'role' en 'role_id'
            $table->renameColumn('role', 'role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Renommer la colonne 'role_id' en 'role' (pour annuler le changement)
            $table->renameColumn('role_id', 'role');
        });
    }
}
