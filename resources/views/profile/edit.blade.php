<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-6 sm:p-8 bg-white shadow-[0_4px_16px_rgba(0,0,0,0.04)] border border-slate-200 sm:rounded-2xl">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white shadow-[0_4px_16px_rgba(0,0,0,0.04)] border border-slate-200 sm:rounded-2xl">
                <div class="max-w-full">
                    @include('profile.partials.update-profile-data-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white shadow-[0_4px_16px_rgba(0,0,0,0.04)] border border-slate-200 sm:rounded-2xl">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
