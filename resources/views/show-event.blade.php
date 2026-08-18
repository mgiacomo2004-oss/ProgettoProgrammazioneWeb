<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dettaglio Evento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

                @if(session('success'))
                    <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:6px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:6px;">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- TITOLO --}}
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $event->title }}
                </h1>

                {{-- DETTAGLI --}}
                <div class="space-y-2 text-gray-700">

                    <p><span class="font-semibold">Descrizione:</span><br>{{ $event->description }}</p>
                    <p><span class="font-semibold">Luogo:</span> {{ $event->location }}</p>
                    <p><span class="font-semibold">Data:</span> {{ $event->formattedEventDate() }}</p>
                    <p><span class="font-semibold">Termine iscrizione:</span> {{ $event->formattedRegistrationDeadline() }}</span>
                    </p>
                    <p><span class="font-semibold">Max partecipanti:</span> {{ $event->max_participants }}</p>
                    <p><span class="font-semibold">Costo:</span> €{{ $event->cost }}</p>
                    <p><span class="font-semibold">Iscritti:</span> {{ $event->users->count() }}</p>

                    <p>
                        <span class="font-semibold">Posti disponibili:</span>
                        {{ $event->availableSpots() }}
                    </p>

                </div>

                <hr>

                @auth
                    @if(Auth::user()->role !== 'admin')

                        <div class="flex gap-3">

                            @if($event->isFinished())

                                <span style="color:#6b7280;font-weight:bold;">
                                    Evento concluso
                                </span>

                            @elseif($event->users->pluck('id')->contains(auth()->id()))

                                <form method="POST" action="/events/{{ $event->id }}/leave">
                                    @csrf
                                    <button style="background:#dc2626;color:white;padding:8px 14px;border-radius:6px;">
                                        Annulla iscrizione
                                    </button>
                                </form>

                            @elseif($event->isClosed())

                                <span style="color:#dc2626;font-weight:bold;">
                                    Termine iscrizioni scaduto
                                </span>

                            @elseif(!$event->isFull())

                                <form method="POST" action="/events/{{ $event->id }}/join">
                                    @csrf
                                    <button style="background:#16a34a;color:white;padding:8px 14px;border-radius:6px;">
                                        Partecipa
                                    </button>
                                </form>

                            @else

                                <span style="color:#dc2626;font-weight:bold;">
                                    Evento pieno
                                </span>

                            @endif

                        </div>

                    @endif
                @endauth
                @if(Auth::check() && Auth::user()->role === 'admin')

                    <hr>

                    <div x-data="{ open: false }">

                        <button @click="open = !open" style="font-size:18px;font-weight:bold;color:#2563eb;">
                            ▶ Iscritti ({{ $event->users->count() }})
                        </button>

                        <div x-show="open" style="margin-top:10px;">

                            @if($event->users->count() > 0)

                                <ul>

                                    @foreach($event->users as $user)
                                        <li>
                                            {{ $user->name }} ({{ $user->email }})
                                        </li>
                                    @endforeach

                                </ul>

                            @else

                                <p>Nessun iscritto.</p>

                            @endif

                        </div>

                    </div>

                @endif
                {{-- AZIONI ADMIN --}}
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <div class="flex gap-3 pt-4">
                        @if(!$event->isFinished())
                            <a href="/events/{{ $event->id }}/edit"
                                style="background:#f59e0b;color:white;padding:8px 14px;border-radius:6px;display:inline-block;">
                                Modifica
                            </a>
                        @endif
                        <form method="POST" action="/events/{{ $event->id }}">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Sei sicuro di voler eliminare questo evento?')"
                                style="background:#374151;color:white;padding:8px 14px;border-radius:6px;">
                                Elimina
                            </button>
                        </form>

                    </div>
                @endif

                {{-- BACK --}}
                <div class="pt-4">
                    <a href="/events" class="text-gray-600 hover:underline">
                        ← Torna alla lista
                    </a>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>