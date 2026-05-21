<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->renameColumn('deletedAt', 'deleted_at');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->renameColumn('deletedAt', 'deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->renameColumn('deleted_at', 'deletedAt');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->renameColumn('deleted_at', 'deletedAt');
        });
    }
};
