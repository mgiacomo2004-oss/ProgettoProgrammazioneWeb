<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $filter = request()->query('filter');
        $search = request()->query('search');
        $statusSearch = strtolower(trim($search));

        $now = now();
        $today = today();
        $currentTime = $now->format('H:i:s');

        $query = Event::with('users')
            ->orderBy('event_date')
            ->orderBy('start_time');


        //stato CONCLUSO
        $finished = function ($q) use ($today, $currentTime) {
            $q->whereDate('event_date', '<', $today)
                ->orWhere(function ($q) use ($today, $currentTime) {
                    $q->whereDate('event_date', $today)
                        ->whereTime('end_time', '<=', $currentTime);
            });
        };

        //stato ANNULLATO
        $cancelled = function ($q) use ($today, $currentTime) {
            $q->whereDate('event_date', $today)
                ->whereTime('start_time', '<=', $currentTime)
                ->whereTime('end_time', '>', $currentTime)
                ->whereDoesntHave('users');
        };

        //stato IN CORSO
        $inProgress = function ($q) use ($today, $currentTime) {
            $q->whereDate('event_date', $today)
                ->whereTime('start_time', '<=', $currentTime)
                ->whereTime('end_time', '>', $currentTime)
                ->whereHas('users');
        };

        //stato CHIUSO
        $closed = function ($q) use ($today, $currentTime) {
            $q->whereDate('registration_deadline', '<=', $today)
                ->where(function ($q) use ($today, $currentTime) {
                    $q->whereDate('event_date', '>', $today)
                        ->orWhere(function ($q) use ($today, $currentTime) {
                            $q->whereDate('event_date', $today)
                                ->whereTime('start_time', '>', $currentTime);
                        });
                });
        };

        //stato PIENO
        $full = function ($q) {
            $q->whereRaw('(
                select count(*)
                from event_user
                where event_user.event_id = events.id
            ) >= events.max_participants');
        };
        //TUTTI 
        if ($filter !== 'history') {

            $query->whereNot(function ($q) use ($finished) {
                $finished($q);
            });

            $query->whereNot(function ($q) use ($cancelled) {
                $cancelled($q);
            });
        }

        // SEARCH
        if ($search) {
            if ($statusSearch === 'aperto') {

                $query->whereNot(function ($q) use ($finished) {
                    $finished($q);
                });

                $query->whereNot(function ($q) use ($cancelled) {
                    $cancelled($q);
                });

                $query->whereNot(function ($q) use ($inProgress) {
                    $inProgress($q);
                });

                $query->whereNot(function ($q) use ($closed) {
                    $closed($q);
                });

                $query->whereNot(function ($q) use ($full) {
                    $full($q);
                });

            } elseif ($statusSearch === 'chiuso') {

                $query->where($closed);

            } elseif ($statusSearch === 'in corso') {

                $query->where($inProgress);

            } elseif ($statusSearch === 'pieno') {

                $query->where($full);

            } elseif ($statusSearch === 'annullato') {

                $query->where($cancelled);

            } elseif ($statusSearch === 'concluso') {

                $query->where($finished);

            } else {

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('event_date', 'like', "%{$search}%")
                        ->orWhere('registration_deadline', 'like', "%{$search}%")
                        ->orWhere('cost', 'like', "%{$search}%")
                        ->orWhere('max_participants', 'like', "%{$search}%");
                });
            }
    
        }

        //filtro DISPONIBILI 
        if ($filter === 'available') {

            $query->whereNot(function ($q) use ($finished) {
                $finished($q);
            });

            $query->whereNot(function ($q) use ($cancelled) {
                $cancelled($q);
            });

            $query->whereNot(function ($q) use ($inProgress) {
                $inProgress($q);
            });

            $query->whereNot(function ($q) use ($closed) {
                $closed($q);
            });

            $query->whereNot(function ($q) use ($full) {
                $full($q);
            });
        }
        
        if ($filter === 'mine') {

            $query->whereHas('users', function ($q) {
                $q->where('users.id', auth()->id());
            });

            $query->whereNot(function ($q) use ($finished) {
                $finished($q);
            });

            $query->whereNot(function ($q) use ($cancelled) {
                $cancelled($q);
            });
        }

        if ($filter === 'history') {

            $query->where(function ($q) use ($finished, $cancelled) {
                $q->where($finished)
                ->orWhere($cancelled);
            });
        }

        $events = $query->paginate(10)->withQueryString();

        return view('events', compact('events'));
    }
    public function create()
    {
        return view('create-event');
    }


    public function store(Request $request)
    {
        $data = $this->validateEvent($request);

        Event::create($data);

        return redirect('/events')->with('success', 'Evento creato con successo!');
    }

    public function show($id)
    {
        return view('show-event', [
            'event' => Event::with('users')->findOrFail($id)
        ]);
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        if ($event->isFinished() || $event->isInProgress() || $event->isCancelled()) {
            return redirect('/events')
                ->with('error', 'Non puoi modificare un evento concluso, in corso o annullato.');
        }
        return view('edit-event', [
            'event' => $event
        ]);
    }

    public function update(Request $request, $id)
    {

        $event = Event::findOrFail($id);

        if ($event->isFinished() || $event->isInProgress() || $event->isCancelled()) {
            return redirect('/events')
                ->with('error', 'Non puoi modificare un evento concluso, in corso o annullato.');
        }
        
        $data = $this->validateEvent($request, $event);
        
        $oldTitle = $event->title;

        $event->update($data);

        foreach ($event->users as $user) {

            $user->notifications()->create([
                'message' => 'L\'evento "' . $oldTitle . '" è stato modificato.'
            ]);

        }

        return redirect('/events')->with('success', 'Evento modificato con successo!');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->isInProgress()) {
            return redirect('/events')
                ->with('error', 'Non puoi eliminare un evento in corso.');
        }

        foreach ($event->users as $user) {

            $user->notifications()->create([
                'message' => 'L\'evento "' . $event->title . '" è stato eliminato.'
            ]);

        }

        $event->delete();

        return redirect('/events')->with('success', 'Evento eliminato con successo!');
    }

    public function join($id)
    {
        $event = Event::with('users')->findOrFail($id);

        $user = auth()->user();

        if (!$user || $user->role === 'admin') {
            return back()->with('error', 'Operazione non consentita.');
        }

        if ($event->isFinished() || $event->isInProgress() || $event->isCancelled()) {
            return back()->with('error', 'Non puoi iscriverti a un evento concluso, in corso o annullato.');
        }

        if ($event->isClosed()) {
            return back()->with('error', 'Termine iscrizioni scaduto.');
        }

        if ($event->users->contains($user->id)) {
            return back()->with('error', 'Sei già iscritto a questo evento.');
        }

        if ($event->isFull()) {
            return back()->with('error', 'Evento pieno.');
        }

        $event->users()->attach($user->id);

        return back()->with('success', 'Iscrizione avvenuta con successo.');
    }

    public function leave($id)
    {
        $event = Event::findOrFail($id);

        $user = auth()->user();

        if ($event->isInProgress() || $event->isFinished() || $event->isCancelled()) {
            return back()->with('error', 'non puoi disiscriverti ad un evento in corso, concluso o annullato.');
        }

        $event->users()->detach($user->id);

        return back()->with('success', 'Iscrizione annullata con successo.');
    }
    private function validateEvent(Request $request, ?Event $event = null)
    {
        $currentParticipants = $event
            ? $event->users()->count()
            : 0;

        return $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'location' => 'required|max:255',
            
            'event_date' => 'required|date|after:today',
            
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            
            'registration_deadline' => 'required|date|before_or_equal:event_date',
            
            'max_participants' => [
                'required',
                'integer',
                'min:' . max(1, $currentParticipants),
            ],
            
            'cost' => 'required|numeric|min:0',
        ]);
    }
}
