<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <h2 class="text-2xl font-semibold mb-4">Detail Transaksi</h2>
                
                @php
                    $statusClass = [
                        'success' => 'bg-green-100 text-green-800',
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'failed' => 'bg-red-100 text-red-800',
                        'expired' => 'bg-gray-100 text-gray-800',
                    ][$transaction->status];
                @endphp

                <div class="border rounded-lg p-6">
                    <p class="mb-2"><strong>Status:</strong> <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">{{ ucfirst($transaction->status) }}</span></p>
                    <p class="mb-2"><strong>Order ID:</strong> {{ $transaction->order_id }}</p>
                    <p class="mb-2"><strong>Produk:</strong> {{ $transaction->product->name }} ({{ $transaction->product->game->name }})</p>
                    <p class="mb-2"><strong>User ID:</strong> {{ $transaction->game_user_id }}</p>
                    <p class="mb-4"><strong>Total Bayar:</strong> Rp {{ number_format($transaction->total_price) }}</p>
                    <a href="{{ route('home') }}" class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
