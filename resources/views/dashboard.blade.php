<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            {{-- Ringkasan jumlah tugas per status --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach (\App\Enums\TaskStatus::cases() as $status)
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <p class="text-2xl font-bold text-gray-900">{{ $counts[$status->value] }}</p>
                        <p class="text-sm text-gray-600">{{ $status->label() }}</p>
                    </div>
                @endforeach
                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <p class="text-2xl font-bold text-red-600">{{ $overdue }}</p>
                    <p class="text-sm text-gray-600">Terlambat</p>
                </div>
            </div>

            {{-- Tugas dengan tenggat terdekat --}}
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Tenggat terdekat</h3>
                    <a href="{{ route('tasks.create') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">+ Tugas baru</a>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse ($upcoming as $task)
                        <x-task-card :task="$task" />
                    @empty
                        <p class="text-sm text-gray-600">Belum ada tugas dengan tenggat. Tambahkan tugas baru.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
