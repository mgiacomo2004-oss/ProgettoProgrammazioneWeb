<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifica Evento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                
                @if($errors->any())
                    <div class="mb-4 p-4 rounded bg-red-100 text-red-800">
                        <ul class="list-disc ml-5">
                            @foreach($errors->all() as $error)
                                <li class="text-red-800">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif      

                <form method="POST" action="/events/{{ $event->id }}" class="space-y-6">

                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Titolo</label>
                        <input type="text" name="title" value="{{ $event->title }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Descrizione</label>
                        <textarea name="description" rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $event->description }}</textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Luogo</label>
                        <input type="text" name="location" value="{{ $event->location }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="form-label">Data</label>
                        <input type="date" name="event_date" value="{{ $event->event_date->format('Y-m-d') }}"
                            class="form-control">
                    </div>
                    
                    <div>
                        <label class="form-label">Ora inizio</label>
                        <input
                            type="time"
                            name="start_time"
                            value="{{ $event->start_time }}"
                            class="form-control"
                        >
                    </div>

                    <div>
                        <label class="form-label">Ora fine</label>
                        <input
                            type="time"
                            name="end_time"
                            value="{{ $event->end_time }}"
                            class="form-control"
                        >
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Termine iscrizione</label>
                        <input type="date" name="registration_deadline"
                            value="{{ $event->registration_deadline->format('Y-m-d') }}" class="form-control">
                    </div>
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Max partecipanti</label>
                        <input type="number" name="max_participants" value="{{ $event->max_participants }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Costo</label>
                        <input type="number" step="0.01" name="cost" value="{{ $event->cost }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="flex gap-3">

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Salva modifiche
                        </button>

                        <a href="/events" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            Annulla
                        </a>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>