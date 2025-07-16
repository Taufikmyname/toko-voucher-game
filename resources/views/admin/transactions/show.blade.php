<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Transaksi: ') }} {{ $transaction->order_id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kolom Kiri: Detail Pesanan -->
                    <div class="md:col-span-2 space-y-4">
                        <h3 class="text-lg font-semibold border-b pb-2">Rincian Pesanan</h3>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Order ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->order_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Produk</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->product->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Game</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->product->game->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Game User ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->game_user_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Transaksi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->created_at->format('d F Y, H:i:s') }}</dd>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Detail Pembayaran & Pelanggan -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold border-b pb-2">Rincian Pembayaran</h3>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Harga</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900">Rp {{ number_format($transaction->total_price) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Metode Pembayaran</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->payment_method ?? 'Belum Dibayar' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($transaction->status == 'success') bg-green-100 text-green-800 @endif
                                @if($transaction->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                @if($transaction->status == 'failed' || $transaction->status == 'expired') bg-red-100 text-red-800 @endif
                                ">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </dd>
                        </div>

                        <h3 class="text-lg font-semibold border-b pb-2 pt-4">Rincian Pelanggan</h3>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->user->name ?? 'Guest' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->user->email ?? 'Tidak terdaftar' }}</dd>
                        </div>
                                                <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->customer_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor HP</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->customer_phone }}</dd>
                        </div>
                    </div>

                <!-- Bagian Aksi Admin (Sekarang menjadi backup) -->
                <div class="p-6 border-t">
                    @if($transaction->status == 'success' && $transaction->voucher_code)
                        <div class="p-4 bg-green-100 text-green-800 rounded-md">
                            <p><strong>Voucher Terkirim Otomatis:</strong> {{ $transaction->voucher_code }}</p>
                        </div>
                    @elseif($transaction->status == 'success' && $transaction->voucher_code == null)
                        <div class="p-4 bg-yellow-100 text-yellow-800 rounded-md">
                            <p><strong>Peringatan:</strong> Pembayaran sukses, tetapi pengiriman voucher otomatis gagal (kemungkinan stok habis).</p>
                            <form action="{{ route('admin.transactions.sendVoucher', $transaction) }}" method="POST" onsubmit="return confirm('Anda yakin ingin mengirimkan voucher secara manual?');" class="mt-2">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                    Coba Kirim Ulang Voucher
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-4 bg-gray-100 text-gray-600 rounded-md">
                            <p>Pembayaran belum selesai atau gagal. Tidak ada aksi yang bisa dilakukan.</p>
                        </div>
                    @endif
                </div>
                </div>
                <div class="p-6 border-t">
                    <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Kembali ke Daftar Transaksi</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>