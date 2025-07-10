<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Total Pendapatan</h3>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalRevenue) }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Transaksi Sukses</h3>
                    <p class="text-3xl font-bold">{{ $successfulTransactions }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Transaksi Pending</h3>
                    <p class="text-3xl font-bold">{{ $pendingTransactions }}</p>
                </div>
            </div>

            <!-- Latest Transactions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold mb-4">5 Transaksi Terakhir</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Order ID</th>
                                    <th class="py-2 px-4 border-b text-left">Produk</th>
                                    <th class="py-2 px-4 border-b text-left">Harga</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestTransactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">{{ $transaction->order_id }}</td>
                                    <td class="py-2 px-4 border-b">{{ $transaction->product->name }}</td>
                                    <td class="py-2 px-4 border-b">Rp {{ number_format($transaction->total_price) }}</td>
                                    <td class="py-2 px-4 border-b">{{ ucfirst($transaction->status) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Belum ada transaksi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>