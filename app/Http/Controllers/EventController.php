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
        $query = Event::with('users')
            ->orderBy('event_date');

        // SEARCH
        if ($search) {

            if ($statusSearch === 'aperto') {

                $query->whereDate('event_date', '>', today())
                    ->where(function ($q) {
                        $q->whereNull('registration_deadline')
                            ->orWhereDate('registration_deadline', '>=', today());
                    })
                    ->whereRaw('(
                select count(*)
                from event_user
                where event_user.event_id = events.id
            ) < events.max_participants');

            } elseif ($statusSearch === 'chiuso') {

                $query->whereDate('event_date', '>', today())
                    ->whereDate('registration_deadline', '<', today());

            } elseif ($statusSearch === 'concluso') {

                $query->whereDate('event_date', '<', today());
            } elseif ($statusSearch === 'in corso') {

                $query->whereDate('event_date', '=', today());
            } elseif ($statusSearch === 'pieno') {

                $query->whereRaw('(
                    select count(*)
                    from event_user
                    where event_user.event_id = events.id
                ) >= events.max_participants');

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
        // FILTER AVAILABLE
        if ($filter === 'available') {

            $query->whereDate('event_date', '>', today())

                ->where(function ($q) {
                    $q->whereNull('registration_deadline')
                        ->orWhereDate('registration_deadline', '>=', today());
                })

                ->whereRaw('(
            select count(*)
            from event_user
            where event_user.event_id = events.id
        ) < events.max_participants');
        }

        // FILTER MINE
        if ($filter === 'mine') {
            $query->whereHas('users', function ($q) {
                $q->where('users.id', auth()->id());
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
        if ($event->isFinished() || $event->isInProgress()) {
            return redirect('/events')
                ->with('error', 'Non puoi modificare un evento concluso o in corso.');
        }
        return view('edit-event', [
            'event' => $event
        ]);
    }

    public function update(Request $request, $id)
    {

        $data = $this->validateEvent($request);

        $event = Event::findOrFail($id);

        if ($event->isFinished() || $event->isInProgress()) {
            return redirect('/events')
                ->with('error', 'Non puoi modificare un evento concluso o in corso.');
        }

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

        if ($event->isFinished() || $event->isInProgress()) {
            return back()->with('error', 'Non puoi iscriverti a un evento concluso o in corso.');
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

        $event->users()->detach($user->id);

        return back()->with('success', 'Iscrizione annullata con successo.');
    }
    private function validateEvent(Request $request)
    {
        return $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'location' => 'required|max:255',
            'event_date' => 'required|date|after:today',
            'registration_deadline' => 'required|date|before_or_equal:event_date',
            'max_participants' => 'required|integer|min:1',
            'cost' => 'required|numeric|min:0',
        ]);
    }
}
