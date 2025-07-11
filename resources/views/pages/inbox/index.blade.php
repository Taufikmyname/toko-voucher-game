<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kotak Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    @forelse ($messages as $message)
                        <a href="{{ $message->link ?? '#' }}" class="block p-4 rounded-lg transition @if(!$message->is_read) bg-blue-50 hover:bg-blue-100 @else bg-white hover:bg-gray-50 @endif border">
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold">{{ $message->title }}</h3>
                                <span class="text-xs text-gray-500 flex-shrink-0 ml-4">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($message->body, 150) }}</p>
                        </a>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500">Kotak masuk Anda kosong.</p>
                        </div>
                    @endforelse

                    <div class="mt-6">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
