<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lista Eventi
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 text-right text-sm text-gray-600">
                🕐 Ora corrente:
                <span id="current-time" class="font-semibold text-gray-900"></span>
            </div>
            @if(session('success'))
                <div class="mb-4 p-4 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded bg-red-100 text-red-800">
                    {{ session('error') }}
                </div>
            @endif
            {{-- FILTRI + SEARCH + ADMIN  --}} 
            <div class="flex flex-col gap-4 mb-6">

                @php
                    $filter = request()->query('filter');
                @endphp

                <div class="flex gap-4 mb-4 text-sm">

                    
                    
                        <a href="/events" class="px-2 py-1 rounded
       {{ !$filter ? 'font-bold underline text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                        Tutti
                         </a>
                    @if(auth()->user()?->role !== 'admin')
                         <a href="/events?filter=available" class="px-2 py-1 rounded
       {{ $filter === 'available' ? 'font-bold underline text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                          Disponibili
                         </a>

                         <a href="/events?filter=mine" class="px-2 py-1 rounded
       {{ $filter === 'mine' ? 'font-bold underline text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                         I miei eventi
                        </a>
                    @endif
                    <a href="/events?filter=history" class="px-2 py-1 rounded
       {{ $filter === 'history' ? 'font-bold underline text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                         Storico
                    </a>

                </div>
                {{-- SEARCH + ADMIN  --}} 
                <div class="flex justify-between items-center">

                    <form method="GET" action="/events" class="flex gap-2">

                        @if(request('filter'))
                            <input type="hidden" name="filter" value="{{ request('filter') }}">
                        @endif

                        <input type="text" name="search" placeholder="Titolo, luogo, data, stato..." value="{{ request('search') }}" class="border rounded px-3 py-2 w-[500px]">

                        <button type="submit" class="px-3 py-2 bg-gray-800 text-white rounded">
                            Cerca
                        </button>

                    </form>

                    @auth
                        @if(Auth::check() && Auth::user()->role === 'admin')
                            <a href="/events/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                Nuovo Evento
                            </a>
                        @endif
                    @endauth

                </div>

            </div>

        </div>

        {{-- TABELLA --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Titolo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Luogo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Data
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Scadenza iscrizione
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Iscritti
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Disponibili
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Costo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Stato
                                </th>
                                @if(auth()->user()?->role !=='admin')
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Iscrizione
                                      </th>
                                @endif
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">

                            @foreach($events as $event)
                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <a href="/events/{{ $event->id }}" class="text-blue-600 hover:underline">
                                            {{ $event->title }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $event->location }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $event->formattedEventDate() }}
                                        <br>
                                        {{ $event->start_time }} - {{ $event->end_time }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $event->formattedRegistrationDeadline() }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $event->users->count() }} / {{ $event->max_participants }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $event->availableSpots() }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        €{{ $event->cost }}
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                         <span class="{{ $event->statusColor() }} font-semibold">
                                           {{ $event->displayStatus() }}
                                        </span>
                                    </td>
                                    
                                    @if(auth()->user()?->role !== 'admin')
                                          <td class="px-6 py-4 text-sm">
                                                 @if($event->users->pluck('id')->contains(auth()->id()))
                                                     <span class="text-blue-600 font-semibold">ISCRITTO</span>
                                                 @else
                                                    <span class="text-gray-500">NON ISCRITTO</span>
                                                 @endif
                                            </td>
                                    @endif

                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    <div class="bg-white px-6 py-4 border-t flex justify-center">
                        {{ $events->links() }}
                    </div>
                </div>

            </div>

        </div>

    </div>
    <script>
        function updateClock() {
            const now = new Date();

            const time = now.toLocaleTimeString('it-IT', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            document.getElementById('current-time').textContent = time;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>
</x-app-layout>