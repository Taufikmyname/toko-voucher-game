<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('admin.transactions.index') }}" class="mb-6">
                    <div class="flex items-center space-x-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Filter berdasarkan Status:</label>
                            <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="all" @selected(request('status') == 'all')>Semua</option>
                                <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                                <option value="success" @selected(request('status') == 'success')>Success</option>
                                <option value="failed" @selected(request('status') == 'failed')>Failed</option>
                                <option value="expired" @selected(request('status') == 'expired')>Expired</option>
                            </select>
                        </div>
                        <div class="pt-6">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Filter</button>
                        </div>
                    </div>
                </form>

                <!-- Transactions Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-2 px-4 border-b text-left">Order ID</th>
                                <th class="py-2 px-4 border-b text-left">Pelanggan</th>
                                <th class="py-2 px-4 border-b text-left">Produk</th>
                                <th class="py-2 px-4 border-b text-left">Harga</th>
                                <th class="py-2 px-4 border-b text-left">Status</th>
                                <th class="py-2 px-4 border-b text-left">Tanggal</th>
                                <th class="py-2 px-4 border-b text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-b text-sm">{{ $transaction->order_id }}</td>
                                <td class="py-2 px-4 border-b">{{ $transaction->user->name ?? 'Guest' }}</td>
                                <td class="py-2 px-4 border-b">{{ $transaction->product->name }} ({{ $transaction->product->game->name }})</td>
                                <td class="py-2 px-4 border-b">Rp {{ number_format($transaction->total_price) }}</td>
                                <td class="py-2 px-4 border-b">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($transaction->status == 'success') bg-green-100 text-green-800 @endif
                                    @if($transaction->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                    @if($transaction->status == 'failed' || $transaction->status == 'expired') bg-red-100 text-red-800 @endif
                                    ">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 border-b text-sm">{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                <td class="py-2 px-4 border-b">
                                    <a href="{{ route('admin.transactions.show', $transaction) }}" class="text-blue-500 hover:text-blue-700">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada data transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
