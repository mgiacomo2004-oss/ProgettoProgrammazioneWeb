<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crea Evento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if($errors->any())
                    <div class="mb-4 p-4 rounded bg-red-100 text-red-800">
                        <ul class="list-disc ml-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/events" class="space-y-6">

                    @csrf

                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            Titolo
                        </label>

                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            Descrizione
                        </label>

                        <textarea
                            name="description"
                            rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            Luogo
                        </label>

                        <input
                            type="text"
                            name="location"
                            value="{{ old('location') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            Data
                        </label>

                        <input
                            type="date"
                            name="event_date"
                            value="{{ old('event_date') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            Ora inizio
                        </label>

                        <input
                            type="time"
                            name="start_time"
                            value="{{ old('start_time') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            Ora fine
                        </label>

                        <input
                            type="time"
                            name="end_time"
                            value="{{ old('end_time') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            Termine iscrizione
                        </label>

                        <input
                            type="date"
                            name="registration_deadline"
                            value="{{ old('registration_deadline') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            Max partecipanti
                        </label>

                        <input
                            type="number"
                            name="max_participants"
                            value="{{ old('max_participants') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            Costo
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="cost"
                            value="{{ old('cost') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                    </div>

                    <div class="flex gap-3">

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                        >
                            Crea Evento
                        </button>

                        <a
                            href="/events"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded"
                        >
                            Annulla
                        </a>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>