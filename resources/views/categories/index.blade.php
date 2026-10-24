<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Kategori</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-flash />

            <form method="POST" action="{{ route('categories.store') }}" class="flex gap-2 rounded-lg bg-white p-4 shadow-sm">
                @csrf
                <div class="flex-1">
                    <x-text-input name="name" class="block w-full" placeholder="Nama kategori baru" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <x-primary-button>Tambah</x-primary-button>
            </form>

            <div class="divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                @forelse ($categories as $category)
                    <div class="flex items-center justify-between px-4 py-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $category->name }}</p>
                            <p class="text-xs text-gray-500">{{ $category->tasks_count }} tugas</p>
                        </div>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini? Tugasnya tidak ikut terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-md px-3 py-2 text-sm text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                @empty
                    <p class="px-4 py-3 text-sm text-gray-600">Belum ada kategori.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
