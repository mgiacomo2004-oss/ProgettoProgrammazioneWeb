<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Notifiche
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @forelse($notifications as $notification)

                    <div class="flex justify-between items-center border-b py-3">

                        <span>
                            {{ $notification->message }}
                        </span>

                        <form method="POST" action="/notifications/{{ $notification->id }}">
                            @csrf
                            @method('DELETE')

                            <button onclick="return confirm('Eliminare questa notifica?')"
                                class="text-red-600 hover:text-red-800">
                                Elimina
                            </button>
                        </form>

                    </div>

                @empty

                    <p>Nessuna notifica.</p>

                @endforelse

            </div>

        </div>
    </div>

</x-app-layout>