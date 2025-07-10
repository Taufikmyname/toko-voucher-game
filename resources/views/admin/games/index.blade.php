<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Game') }}
            </h2>
            <a href="{{ route('admin.games.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Tambah Game</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Thumbnail</th>
                            <th class="py-2 px-4 border-b">Nama</th>
                            <th class="py-2 px-4 border-b">Kategori</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($games as $game)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b"><img src="{{ asset('storage/' . $game->thumbnail) }}" alt="{{ $game->name }}" class="h-16 w-16 object-cover rounded-md"></td>
                            <td class="py-2 px-4 border-b">{{ $game->name }}</td>
                            <td class="py-2 px-4 border-b">{{ $game->category }}</td>
                            <td class="py-2 px-4 border-b">{{ $game->is_active ? 'Aktif' : 'Non-Aktif' }}</td>
                            <td class="py-2 px-4 border-b">
                                <a href="{{ route('admin.games.edit', $game) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                                <form action="{{ route('admin.games.destroy', $game) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus game ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada data game.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $games->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>