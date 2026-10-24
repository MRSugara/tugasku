@props(['task'])

{{--
    Kartu satu tugas. Dipakai ulang di halaman Dashboard dan halaman Tugas.
    Tombol "Mulai"/"Selesai" mengubah status lewat AJAX (lihat resources/js/app.js).
--}}
@php
    $next = $task->status->next();
    $done = $task->status === \App\Enums\TaskStatus::Done;
@endphp

<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm" data-task-card>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p data-task-title @class(['font-semibold text-gray-900', 'line-through text-gray-400' => $done])>
                {{ $task->title }}
            </p>
            @if ($task->description)
                <p class="mt-1 line-clamp-2 text-sm text-gray-600">{{ $task->description }}</p>
            @endif
        </div>
        <x-status-badge :status="$task->status" class="shrink-0" />
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600">
        <x-priority-badge :priority="$task->priority" />
        @if ($task->category)
            <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 font-medium text-indigo-700">{{ $task->category->name }}</span>
        @endif
        @if ($task->due_date)
            <span @class(['font-medium', 'text-red-600' => $task->isOverdue()])>
                Tenggat: {{ $task->due_date->translatedFormat('d M Y') }}{{ $task->isOverdue() ? ' (terlambat)' : '' }}
            </span>
        @endif
    </div>

    <div class="mt-4 flex items-center gap-2">
        @if ($next)
            <button type="button"
                    data-next-status="{{ route('tasks.status', $task) }}"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                {{ $next === \App\Enums\TaskStatus::InProgress ? 'Mulai' : 'Selesai' }}
            </button>
        @endif

        <a href="{{ route('tasks.edit', $task) }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Ubah</a>

        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="ml-auto"
              onsubmit="return confirm('Hapus tugas ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-md px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Hapus</button>
        </form>
    </div>
</div>
