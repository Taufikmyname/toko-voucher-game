<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-4">Top Up {{ $game->name }}</h2>
                
                <form id="payment-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <!-- Kolom Kiri: Info Game & User ID -->
                        <div>
                            <img src="{{ asset('storage/' . $game->thumbnail) }}" alt="{{ $game->name }}" class="w-full rounded-lg mb-4">
                            
                            <h3 class="font-semibold mb-2">1. Masukkan Data Akun</h3>
                            <div class="space-y-4">
                                <div class="flex gap-2">
                                    <input type="text" name="game_user_id" id="game_user_id" placeholder="User ID" class="w-full rounded-md border-gray-300" required>
                                </div>
                            </div>
                            
                            <h3 class="font-semibold mb-2 mt-6">2. Lengkapi Data Kontak</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="customer_email" class="sr-only">Email</label>
                                    <input type="email" name="customer_email" id="customer_email" placeholder="Alamat Email" class="w-full rounded-md border-gray-300" value="{{ auth()->user()->email ?? '' }}" required>
                                    <p class="text-xs text-gray-500 mt-1">Kami akan mengirimkan bukti pembayaran ke email ini.</p>
                                </div>
                                <div>
                                    <label for="customer_phone" class="sr-only">Nomor HP</label>
                                    <input type="tel" name="customer_phone" id="customer_phone" placeholder="Nomor HP (Contoh: 08123456789)" class="w-full rounded-md border-gray-300" required>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Pilih Produk -->
                        <div>
                            <h3 class="font-semibold mb-2">3. Pilih Nominal Top Up</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="product-list">
                                @foreach($products as $product)
                                <label class="border rounded-lg p-4 text-center cursor-pointer has-[:checked]:bg-blue-500 has-[:checked]:text-white has-[:checked]:border-blue-500 transition-all">
                                    <input type="radio" name="product_id" value="{{ $product->id }}" class="sr-only" required>
                                    <span class="font-bold block">{{ $product->name }}</span>
                                    <span class="text-sm">Rp {{ number_format($product->price) }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mt-8">
                        <h3 class="font-semibold mb-2">4. Lakukan Pembayaran</h3>
                        <button type="submit" id="pay-button" class="w-full bg-green-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-600 transition-all disabled:bg-gray-400">
                            Beli Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('payment-form').addEventListener('submit', function (event) {
            event.preventDefault();
            const payButton = document.getElementById('pay-button');
            payButton.disabled = true;
            payButton.innerText = 'Memproses...';

            const formData = new FormData(this);

            fetch('{{ route("checkout") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData
            }).then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert('Terjadi kesalahan: ' + data.error);
                    payButton.disabled = false;
                    payButton.innerText = 'Beli Sekarang';
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
                        payButton.innerText = 'Beli Sekarang';
                    },
                    onClose: function(){
                        payButton.disabled = false;
                        payButton.innerText = 'Beli Sekarang';
                    }
                });
            }).catch(error => {
                console.error('Error:', error);
                alert('Tidak dapat memproses pembayaran.');
                payButton.disabled = false;
                payButton.innerText = 'Beli Sekarang';
            });
        });
    </script>
    @endpush
</x-app-layout>