<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Tugas</h2>
            <a href="{{ route('tasks.create') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">+ Tugas baru</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <x-flash />

            {{-- Form pencarian & filter (method GET, jadi tersimpan di URL) --}}
            <form method="GET" action="{{ route('tasks.index') }}" class="mb-6 grid gap-3 sm:grid-cols-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul tugas..."
                       class="rounded-md border-gray-300 shadow-sm sm:col-span-2">

                <select name="status" class="rounded-md border-gray-300 shadow-sm">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>

                <select name="category" class="rounded-md border-gray-300 shadow-sm">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <div class="flex gap-2 sm:col-span-4">
                    <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white">Terapkan</button>
                    <a href="{{ route('tasks.index') }}" class="rounded-md px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">Reset</a>
                </div>
            </form>

            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($tasks as $task)
                    <x-task-card :task="$task" />
                @empty
                    <p class="text-sm text-gray-600">Tidak ada tugas yang cocok.</p>
                @endforelse
            </div>

            <div class="mt-6">{{ $tasks->links() }}</div>
        </div>
    </div>
</x-app-layout>
