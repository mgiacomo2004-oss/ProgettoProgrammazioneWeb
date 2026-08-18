<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        // 1. EVENTO APERTO
        Event::create([
            'title' => 'Evento Aperto',
            'description' => 'Evento di esempio attualmente aperto alle iscrizioni.',
            'location' => 'Brescia',
            'event_date' => now()->addDays(10),
            'registration_deadline' => now()->addDays(5),
            'max_participants' => 20,
            'cost' => 10,
        ]);

        // 2. EVENTO PIENO
        $fullEvent = Event::create([
            'title' => 'Evento Pieno',
            'description' => 'Evento di esempio con tutti i posti occupati.',
            'location' => 'Milano',
            'event_date' => now()->addDays(15),
            'registration_deadline' => now()->addDays(7),
            'max_participants' => 2,
            'cost' => 15,
        ]);

         $admin = User::factory()->create([
             'name' => 'Admin',
             'email' => 'admin@test.it',
             'password' => 'password',
             'role' => 'admin',
        ]);
         $user1 = User::factory()->create([
             'name' => 'Utente Test 1',
             'email' => 'utente1@test.it',
             'password' => 'password',
             'role' => 'user',
        ]);

        $user2 = User::factory()->create([
             'name' => 'Utente Test 2',
             'email' => 'utente2@test.it',
             'password' => 'password',
             'role' => 'user',
        ]);
        $fullEvent->users()->attach([
            $user1->id,
            $user2->id,
        ]);

        // 3. EVENTO CHIUSO
        Event::create([
            'title' => 'Evento Chiuso',
            'description' => 'Evento di esempio con iscrizioni scadute.',
            'location' => 'Verona',
            'event_date' => now()->addDays(10),
            'registration_deadline' => now()->subDays(2),
            'max_participants' => 20,
            'cost' => 20,
        ]);

        // 4. EVENTO IN CORSO
        Event::create([
            'title' => 'Evento In Corso',
            'description' => 'Evento di esempio che si svolge oggi.',
            'location' => 'Bergamo',
            'event_date' => today(),
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 5,
        ]);

        // 5. EVENTO CONCLUSO
        Event::create([
            'title' => 'Evento Concluso',
            'description' => 'Evento di esempio già terminato.',
            'location' => 'Mantova',
            'event_date' => now()->subDays(5),
            'registration_deadline' => now()->subDays(10),
            'max_participants' => 20,
            'cost' => 10,
        ]);
    }
}
