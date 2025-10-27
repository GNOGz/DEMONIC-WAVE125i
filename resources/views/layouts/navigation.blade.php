<nav x-data="{ open: false }" class="bg-white border-b border-white">
    <!-- Primary Navigation Menu -->
    <div id="entire navbar" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 mb-6">
        <div id="Dropdown and Webname" class="flex justify-center items-center gap-6 w-full">
            <h2 class="text-[#1100FF] text-5xl font-bold">DEMONIC WAVE 125i</h2>
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 absolute right-0 mt-20 -translate-y-1/2 mr-10 items-center justify-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->fname . " " . Auth::user()->lname }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @php
                    $user = Auth::user();
                    $photoUrl = $user->profile_img
                        ? route('user.photo', ['filename' => $user->profile_img])
                        : asset('images/profile/default-photo.jpg'); // default photo path in private folder
                @endphp
                <div class="mt-1">
                    <img src="{{$photoUrl}}" alt="Profile Photo" class="w-16 h-16 object-cover rounded-full" />
                </div>
            </div>
        </div>

        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden">
            <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Navigation Links -->
        <div class="flex justify-center h-16 mt-6 border-b border-black p-2">
            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-2xl">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="!text-2xl">
                    {{ __('Dashboard') }}
                </x-nav-link>
                <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="!text-2xl">
                    {{ __('Products') }}
                </x-nav-link>
                <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.index')" class="!text-2xl">
                    {{ __('Cart') }}
                </x-nav-link>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Products') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>