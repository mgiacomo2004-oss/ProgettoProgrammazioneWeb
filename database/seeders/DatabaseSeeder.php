<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
      // UTENTI

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


       //EVENTO APERTO

        Event::create([
            'title' => 'Evento Aperto',
            'description' => 'Evento futuro attualmente aperto alle iscrizioni.',
            'location' => 'Brescia',
            'event_date' => now()->addDays(10),
            'start_time' => '18:00',
            'end_time' => '22:00',
            'registration_deadline' => today()->addDays(5),
            'max_participants' => 20,
            'cost' => 10,
        ]);


      //EVENTO CHIUSO - DEADLINE OGGI

        Event::create([
            'title' => 'Evento Chiuso - Deadline Oggi',
            'description' => 'La deadline è scaduta ieri notte.',
            'location' => 'Brescia',
            'event_date' => today()->addDays(3),
            'start_time' => '18:00',
            'end_time' => '20:00',
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 8,
        ]);


        //EVENTO IN CORSO - CON ISCRITTO

        $inProgressEvent = Event::create([
            'title' => 'Evento In Corso',
            'description' => 'Evento attualmente in corso con un iscritto.',
            'location' => 'Bergamo',
            'event_date' => today(),
            'start_time' => now()->subHour()->format('H:i'),
            'end_time' => now()->addHour()->format('H:i'),
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 5,
        ]);
        $inProgressEvent->users()->attach($user1->id);


        
        // EVENTO CONCLUSO - TERMINATO POCO FA
        $finishedToday = Event::create([
            'title' => 'Evento Concluso - Oggi',
            'description' => 'Evento terminato poco fa.',
            'location' => 'Mantova',
            'event_date' => today(),
            'start_time' => now()->subHours(2)->format('H:i'),
            'end_time' => now()->subMinutes(5)->format('H:i'),
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 10,
        ]);

        $finishedToday->users()->attach($user1->id);


        // EVENTO CONCLUSO - DATA PASSATA
        $finishedEvent = Event::create([
            'title' => 'Evento Concluso - Data Passata',
            'description' => 'Evento terminato nei giorni precedenti.',
            'location' => 'Mantova',
            'event_date' => today()->subDays(5),
            'start_time' => '18:00',
            'end_time' => '21:00',
            'registration_deadline' => today()->subDays(10),
            'max_participants' => 20,
            'cost' => 10,
        ]);

        $finishedEvent->users()->attach($user1->id);


        // EVENTO ANNULLATO - SENZA ISCRITTI
        Event::create([
            'title' => 'Evento Annullato',
            'description' => 'Evento di oggi senza iscritti.',
            'location' => 'Bergamo',
            'event_date' => today(),
            'start_time' => now()->subHour()->format('H:i'),
            'end_time' => now()->addMinutes(30)->format('H:i'),
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 5,
        ]);


      
        // EVENTO PIENO + DEADLINE FUTURA
        $fullFutureEvent = Event::create([
            'title' => 'Evento Pieno - Deadline Futura',
            'description' => 'Evento pieno ma con iscrizioni ancora teoricamente aperte.',
            'location' => 'Milano',
            'event_date' => today()->addDays(10),
            'start_time' => '19:00',
            'end_time' => '21:00',
            'registration_deadline' => today()->addDays(2),
            'max_participants' => 1,
            'cost' => 12,
        ]);

        $fullFutureEvent->users()->attach($user1->id);


        // EVENTO CHIUSO - DATA MOLTO FUTURA
        Event::create([
            'title' => 'Evento Chiuso - Data Futura',
            'description' => 'Deadline già passata nonostante l’evento sia molto lontano.',
            'location' => 'Verona',
            'event_date' => today()->addMonth(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'registration_deadline' => today()->subDay(),
            'max_participants' => 50,
            'cost' => 25,
        ]);

        /*
        | test per le transizioni
        */
        
        //EVENTO CHIUSO -> ANNULLATO

        Event::create([
            'title' => 'Evento - Inizio Vicino',
            'description' => 'Evento senza iscritti che sta per iniziare.',
            'location' => 'Brescia',
            'event_date' => today(),
            'start_time' => now()->addMinutes(5)->format('H:i'),
            'end_time' => now()->addMinutes(35)->format('H:i'),
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 10,
        ]);

        //EVENTO IN CORSO -> CONCLUSO

        $endingEvent = Event::create([
            'title' => 'Evento - Fine Vicina',
            'description' => 'Evento in corso che sta per terminare.',
            'location' => 'Bergamo',
            'event_date' => today(),
            'start_time' => now()->subMinutes(30)->format('H:i'),
            'end_time' => now()->addMinutes(5)->format('H:i'),
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 5,
        ]);

        $endingEvent->users()->attach($user1->id);

        //EVENTO CHIUSO -> IN CORSO

        $startingEvent = Event::create([
            'title' => 'Evento - Inizio Vicino con Iscritto',
            'description' => 'Evento con un iscritto che sta per iniziare.',
            'location' => 'Brescia',
            'event_date' => today(),
            'start_time' => now()->addMinutes(5)->format('H:i'),
            'end_time' => now()->addMinutes(35)->format('H:i'),
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 10,
        ]);

        $startingEvent->users()->attach($user1->id);

        //EVENTO PIENO -> corso

        $fullEvent = Event::create([
            'title' => 'Evento Pieno - Inizio Vicino',
            'description' => 'Evento pieno che sta per iniziare.',
            'location' => 'Milano',
            'event_date' => today(),
            'start_time' => now()->addMinutes(5)->format('H:i'),
            'end_time' => now()->addMinutes(35)->format('H:i'),
            'registration_deadline' => today(),
            'max_participants' => 2,
            'cost' => 15,
        ]);

        $fullEvent->users()->attach([
            $user1->id,
            $user2->id,
        ]);
    }
}