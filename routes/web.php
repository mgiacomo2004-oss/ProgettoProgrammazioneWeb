<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;


//public routes

Route::get('/', function () {
    return view('welcome');
})->name('home');

//Authenticated routes

Route::middleware('auth')->group(function () {
    Route::get('/events', [EventController::class, 'index'])
        ->name('events.index');

    Route::get('/events/{id}', [EventController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('events.show');


    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');

    Route::post('/events/{id}/join', [EventController::class, 'join'])
        ->name('events.join');

    Route::post('/events/{id}/leave', [EventController::class, 'leave'])
        ->name('events.leave');


  //profile routes

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});




//Admin routes


Route::middleware(['auth', 'is_admin'])->group(function () {

    Route::get('/events/create', [EventController::class, 'create'])
        ->name('events.create');

    Route::post('/events', [EventController::class, 'store'])
        ->name('events.store');

    Route::get('/events/{id}/edit', [EventController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('events.edit');

    Route::put('/events/{id}', [EventController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('events.update');

    Route::delete('/events/{id}', [EventController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('events.destroy');
});



Route::get('/dashboard', function () {
    return redirect()->route('events.index');
})->middleware(['auth', 'verified'])->name('dashboard');


require __DIR__ . '/auth.php';