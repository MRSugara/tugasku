<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Tugas baru</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('tasks.store') }}" class="rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @include('tasks._form')
            </form>
        </div>
    </div>
</x-app-layout>
