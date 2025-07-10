<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Stok Voucher') }}
            </h2>
            <a href="{{ route('admin.vouchers.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Tambah Stok</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Kode Voucher</th>
                            <th class="py-2 px-4 border-b">Produk</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vouchers as $voucher)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b font-mono">{{ $voucher->code }}</td>
                            <td class="py-2 px-4 border-b">{{ $voucher->product->game->name }} - {{ $voucher->product->name }}</td>
                            <td class="py-2 px-4 border-b">
                                @if($voucher->is_used)
                                    <span class="text-red-500">Terpakai</span>
                                @else
                                    <span class="text-green-500">Tersedia</span>
                                @endif
                            </td>
                            <td class="py-2 px-4 border-b">
                                <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" onsubmit="return confirm('Anda yakin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Belum ada stok voucher.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $vouchers->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>