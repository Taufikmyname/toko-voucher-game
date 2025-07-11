<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    <!-- resources/views/profile/edit.blade.php -->
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Notifikasi Browser') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Aktifkan notifikasi browser untuk mendapatkan pembaruan penting secara langsung.') }}
                    </p>
                </header>

                <div class="mt-6">
                    <x-primary-button id="enable-notifications-button">
                        {{ __('Aktifkan Notifikasi') }}
                    </x-primary-button>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
