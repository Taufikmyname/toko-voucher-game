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
    <label for="title" class="block text-sm font-medium text-gray-700">Judul Banner</label>
    <input type="text" name="title" id="title" value="{{ old('title', $banner->title ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
</div>

<div class="mb-4">
    <label for="link_url" class="block text-sm font-medium text-gray-700">URL Link (Opsional)</label>
    <input type="url" name="link_url" id="link_url" value="{{ old('link_url', $banner->link_url ?? '') }}" placeholder="https://example.com" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
</div>

<div class="mb-4">
    <label for="image_path" class="block text-sm font-medium text-gray-700">Gambar Banner</label>
    <input type="file" name="image_path" id="image_path" class="mt-1 block w-full">
    @isset($banner->image_path)
    <div class="mt-2">
        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Current Image" class="h-24 object-contain rounded-md">
    </div>
    @endisset
</div>

<div class="mb-4">
    <label for="is_active" class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true)) class="rounded border-gray-300 text-blue-600 shadow-sm">
        <span class="ml-2 text-sm text-gray-600">Aktifkan Banner</span>
    </label>
</div>

<div class="flex items-center justify-end mt-4">
    <a href="{{ route('admin.banners.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
        {{ isset($banner) ? 'Perbarui' : 'Simpan' }}
    </button>
</div>