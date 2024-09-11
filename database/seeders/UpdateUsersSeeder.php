<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;


class UpdateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();
        $users = User::all();

        foreach ($users as $user) {
            // Mettre à jour chaque utilisateur avec un email et une photo fictifs
            $user->update([
                'email' => $faker->unique()->safeEmail,
                'photo' => $faker->imageUrl(640, 480, 'people', true), // URL d'image fictive
            ]);
        }
    }
}