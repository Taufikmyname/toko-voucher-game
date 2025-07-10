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
    <label for="name" class="block text-sm font-medium text-gray-700">Nama Game</label>
    <input type="text" name="name" id="name" value="{{ old('name', $game->name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
</div>

<div class="mb-4">
    <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
    <input type="text" name="category" id="category" value="{{ old('category', $game->category ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
</div>

<div class="mb-4">
    <label for="thumbnail" class="block text-sm font-medium text-gray-700">Thumbnail</label>
    <input type="file" name="thumbnail" id="thumbnail" class="mt-1 block w-full">
    @isset($game->thumbnail)
    <div class="mt-2">
        <img src="{{ asset('storage/' . $game->thumbnail) }}" alt="Current Thumbnail" class="h-24 w-24 object-cover rounded-md">
    </div>
    @endisset
</div>

<div class="mb-4">
    <label for="is_active" class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $game->is_active ?? true)) class="rounded border-gray-300 text-blue-600 shadow-sm">
        <span class="ml-2 text-sm text-gray-600">Aktifkan Game</span>
    </label>
</div>

<div class="flex items-center justify-end mt-4">
    <a href="{{ route('admin.games.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
        {{ isset($game) ? 'Perbarui' : 'Simpan' }}
    </button>
</div>