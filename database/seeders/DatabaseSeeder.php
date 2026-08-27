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


       //EVENTO PIENO

        $fullEvent = Event::create([
            'title' => 'Evento Pieno',
            'description' => 'Evento futuro con tutti i posti occupati.',
            'location' => 'Milano',
            'event_date' => now()->addDays(15),
            'start_time' => '20:00',
            'end_time' => '23:00',
            'registration_deadline' => today()->addDays(7),
            'max_participants' => 2,
            'cost' => 15,
        ]);

        $fullEvent->users()->attach([
            $user1->id,
            $user2->id,
        ]);


      //EVENTO CHIUSO

        $closedEvent = Event::create([
            'title' => 'Evento Chiuso',
            'description' => 'Evento futuro con iscrizioni scadute.',
            'location' => 'Verona',
            'event_date' => now()->addDays(10),
            'start_time' => '18:00',
            'end_time' => '21:00',
            'registration_deadline' => today()->subDays(2),
            'max_participants' => 20,
            'cost' => 20,
        ]);

        $closedEvent->users()->attach($user1->id);


        //EVENTO ANNULLATO

        Event::create([
            'title' => 'Evento Annullato',
            'description' => 'Evento in corso senza iscritti.',
            'location' => 'Bergamo',
            'event_date' => today(),
            'start_time' => now()->subHour()->format('H:i'),
            'end_time' => now()->addHour()->format('H:i'),
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 5,
        ]);


        //EVENTO IN CORSO

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


        //EVENTO CONCLUSO

        $finishedEvent = Event::create([
            'title' => 'Evento Concluso',
            'description' => 'Evento terminato oggi.',
            'location' => 'Mantova',
            'event_date' => today(),
            'start_time' => now()->subHours(3)->format('H:i'),
            'end_time' => now()->subHour()->format('H:i'),
            'registration_deadline' => today()->subDays(2),
            'max_participants' => 20,
            'cost' => 10,
        ]);

        $finishedEvent->users()->attach($user1->id);


        //EVENTO APERTO - DEADLINE OGGI

        Event::create([
            'title' => 'Evento Aperto - Deadline Oggi',
            'description' => 'Evento futuro con deadline fissata a oggi.',
            'location' => 'Brescia',
            'event_date' => today()->addDays(3),
            'start_time' => '18:00',
            'end_time' => '20:00',
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 8,
        ]);


        //EVENTO PIENO CON DEADLINE FUTURA

        $fullFutureEvent = Event::create([
            'title' => 'Evento Pieno - Deadline Futura',
            'description' => 'Evento pieno con deadline ancora valida.',
            'location' => 'Milano',
            'event_date' => today()->addDays(5),
            'start_time' => '19:00',
            'end_time' => '21:00',
            'registration_deadline' => today()->addDays(2),
            'max_participants' => 1,
            'cost' => 12,
        ]);

        $fullFutureEvent->users()->attach($user2->id);


        //EVENTO CHIUSO MOLTO LONTANO

        Event::create([
            'title' => 'Evento Chiuso - Data Futura',
            'description' => 'Evento molto futuro ma con iscrizioni già chiuse.',
            'location' => 'Verona',
            'event_date' => today()->addDays(30),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'registration_deadline' => today()->subDay(),
            'max_participants' => 50,
            'cost' => 25,
        ]);


        //EVENTO FUTURO CON INIZIO VICINO 

        Event::create([
            'title' => 'Evento - Inizio Vicino',
            'description' => 'Evento che inizierà tra poco.',
            'location' => 'Brescia',
            'event_date' => today(),
            'start_time' => now()->addMinutes(30)->format('H:i'),
            'end_time' => now()->addHours(2)->format('H:i'),
            'registration_deadline' => today(),
            'max_participants' => 20,
            'cost' => 10,
        ]);
    }
}