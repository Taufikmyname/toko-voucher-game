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

                    <!-- Tombol Coba Bayar Lagi -->
                    @if(in_array($transaction->status, ['pending', 'failed', 'expired']))
                        <div class="mt-6">
                            <button id="pay-button" class="w-full bg-orange-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-orange-600 transition-all">
                                Coba Bayar Lagi
                            </button>
                        </div>
                    @endif
                    
                    @if($transaction->voucher_code)
                        <div class="mt-4 pt-4 border-t">
                            <p class="text-sm text-gray-500">Kode Voucher Anda:</p>
                            <p class="text-2xl font-bold font-mono bg-gray-100 p-3 rounded-lg inline-block mt-1">{{ $transaction->voucher_code }}</p>
                        </div>
                    @endif

                    <a href="{{ route('home') }}" class="mt-6 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip untuk Coba Bayar Lagi -->
    @push('scripts')
    <script>
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.addEventListener('click', function (event) {
                event.preventDefault();
                
                payButton.disabled = true;
                payButton.innerText = 'Memproses...';

                fetch("{{ route('transaction.retry', $transaction) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => response.json())
                .then(data => {
                    if(data.error) {
                        alert('Terjadi kesalahan: ' + data.error);
                        payButton.disabled = false;
                        payButton.innerText = 'Coba Bayar Lagi';
                        return;
                    }
                    
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            window.location.href = `/transaction/${result.order_id}`;
                        },
                        onPending: function(result){
                            window.location.href = `/transaction/${result.order_id}`;
                        },
                        onError: function(result){
                            alert('Pembayaran Gagal!');
                            payButton.disabled = false;
                            payButton.innerText = 'Coba Bayar Lagi';
                        },
                        onClose: function(){
                            payButton.disabled = false;
                            payButton.innerText = 'Coba Bayar Lagi';
                        }
                    });
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Tidak dapat memproses pembayaran.');
                    payButton.disabled = false;
                    payButton.innerText = 'Coba Bayar Lagi';
                });
            });
        }
    </script>
    @endpush
</x-app-layout>