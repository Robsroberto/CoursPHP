<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
    * Run the database seeds.
    */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => bcrypt('admin123')
        ]);
        $medecin = User::create([
            'name' => 'Dr. Alpha',
            'email' => 'medecin@test.com',
            'role' => 'medecin',
            'password' => bcrypt('password')
        ]);
        Service::create([
            'titre' => 'Consultation générale',
            'description' => 'Consultation de base.',
            'prix' => 15000,
            'duree' => 30,
            'statut' => 'actif',
            'medecin_id' => $medecin->id
        ]);
    }
}
