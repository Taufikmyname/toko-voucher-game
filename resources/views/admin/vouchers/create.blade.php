<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Stok Voucher') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.vouchers.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="product_id" class="block text-sm font-medium text-gray-700">Produk</label>
                        <select name="product_id" id="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">Pilih Produk</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->game->name }} - {{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="codes" class="block text-sm font-medium text-gray-700">Kode Voucher</label>
                        <textarea name="codes" id="codes" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono" placeholder="Masukkan satu kode per baris, atau pisahkan dengan koma/spasi" required></textarea>
                        <p class="text-xs text-gray-500 mt-1">Anda bisa memasukkan banyak kode sekaligus.</p>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.vouchers.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Simpan Stok</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
