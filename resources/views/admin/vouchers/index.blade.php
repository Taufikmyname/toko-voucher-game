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
                <!-- Filter Form -->
                <form method="GET" action="{{ route('admin.vouchers.index') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Filter Status:</label>
                            <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="all" @selected(request('status') == 'all')>Semua</option>
                                <option value="tersedia" @selected(request('status') == 'tersedia')>Tersedia</option>
                                <option value="terpakai" @selected(request('status') == 'terpakai')>Terpakai</option>
                            </select>
                        </div>
                        <div>
                            <label for="game_id" class="block text-sm font-medium text-gray-700">Filter Game:</label>
                            <select name="game_id" id="game_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Semua Game</option>
                                @foreach ($games as $game)
                                    <option value="{{ $game->id }}" @selected(request('game_id') == $game->id)>{{ $game->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Filter</button>
                        </div>
                    </div>
                </form>

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
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Terpakai
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Tersedia
                                    </span>
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
                            <td colspan="4" class="text-center py-4">Tidak ada data voucher yang cocok.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $vouchers->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>