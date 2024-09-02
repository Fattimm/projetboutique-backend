<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dette;

class DetteSeeder extends Seeder
{
    /**
     * Exécuter les seeds de la base de données.
     *
     * @return void
     */
    public function run()
    {
        // Crée 10 dettes à l'aide de la factory
        Dette::factory()->count(10)->create();
    }
}
