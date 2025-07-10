@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-4">
    <label for="game_id" class="block text-sm font-medium text-gray-700">Game</label>
    <select name="game_id" id="game_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        <option value="">Pilih Game</option>
        @foreach($games as $game)
            <option value="{{ $game->id }}" @selected(old('game_id', $product->game_id ?? '') == $game->id)>
                {{ $game->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk (cth: 100 Diamonds)</label>
    <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
</div>

<div class="mb-4">
    <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rupiah)</label>
    <input type="number" name="price" id="price" value="{{ old('price', $product->price ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
</div>

<div class="mb-4">
    <label for="api_product_code" class="block text-sm font-medium text-gray-700">Kode Produk API (Opsional)</label>
    <input type="text" name="api_product_code" id="api_product_code" value="{{ old('api_product_code', $product->api_product_code ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
</div>

<div class="mb-4">
    <label for="is_active" class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="rounded border-gray-300 text-blue-600 shadow-sm">
        <span class="ml-2 text-sm text-gray-600">Aktifkan Produk</span>
    </label>
</div>

<div class="flex items-center justify-end mt-4">
    <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
        {{ isset($product) ? 'Perbarui' : 'Simpan' }}
    </button>
</div>
