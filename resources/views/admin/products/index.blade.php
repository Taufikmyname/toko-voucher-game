<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Produk') }}
            </h2>
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Tambah Produk</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Nama Produk</th>
                            <th class="py-2 px-4 border-b">Game</th>
                            <th class="py-2 px-4 border-b">Harga</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b">{{ $product->name }}</td>
                            <td class="py-2 px-4 border-b">{{ $product->game->name }}</td>
                            <td class="py-2 px-4 border-b">Rp {{ number_format($product->price) }}</td>
                            <td class="py-2 px-4 border-b">{{ $product->is_active ? 'Aktif' : 'Non-Aktif' }}</td>
                            <td class="py-2 px-4 border-b">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada data produk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
