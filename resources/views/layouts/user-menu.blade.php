<div class="flex items-center space-x-4">
    <div class="text-right hidden sm:block">
        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
        <p class="text-xs text-gray-500">{{ Auth::user()->username }}</p>
    </div>
    <div class="relative">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center focus:outline-none transition duration-150 ease-in-out">
                    <img class="h-8 w-8 rounded-full object-cover border" src="{{ Auth::user()->foto_profil ? asset('storage/'.Auth::user()->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" alt="{{ Auth::user()->name }}">
                    <svg class="ml-1 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</div>
