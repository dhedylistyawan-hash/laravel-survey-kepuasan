<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @auth
                        <a href="{{ route('dashboard') }}">
                            <img src="/pusbinjfmkg.png" alt="Pusbin JF MKG" class="block h-9 w-auto" />
                        </a>
                    @else
                        <div class="flex items-center gap-3">
                            <img src="/pusbinjfmkg.png" alt="Pusbin JF MKG" class="block h-9 w-auto" />
                            <div class="hidden md:block">
                                <h1 class="text-sm font-semibold text-gray-800">Pusbin JF MKG</h1>
                                <p class="text-xs text-gray-500">Survey Kepuasan Layanan</p>
                            </div>
                        </div>
                    @endauth
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if(Auth::user()->role === 'admin')
                    <x-nav-link :href="route('admin.surveys.index')" :active="request()->routeIs('admin.surveys.*')" class="nav-link">
                        {{ __('Manajemen Survei') }}
                    </x-nav-link>
                    @endif
                    @endauth

                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="nav-link">
                            {{ __('Kategori') }}
                        </x-nav-link>
                        <x-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.*')" class="nav-link">
                            {{ __('Pertanyaan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('survey-analysis.index')" :active="request()->routeIs('survey-analysis.*')" class="nav-link">
                            {{ __('Analisis Survey') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.backup.index')" :active="request()->routeIs('admin.backup.*')" class="nav-link">
                            {{ __('Backup Database') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('survey.index')" :active="request()->routeIs('survey.*')" class="nav-link">
                        {{ __('Isi Survei') }}
                    </x-nav-link>
                    @if(Auth::guest() && (request()->routeIs('survey.*') || request()->routeIs('survey.guide')))
                        <x-nav-link :href="route('survey.guide')" :active="request()->routeIs('survey.guide')">
                            {{ __('Panduan Pengisian') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-2 text-xs text-gray-400">({{ Auth::user()->role }})</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        @if(Auth::user()->role === 'admin')
                        <x-dropdown-link :href="route('admin.change-password.form')">
                            {{ __('Ganti Password') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('export.pdf.documentation')" target="_blank">
                            {{ __('Download Dokumentasi Admin (PDF)') }}
                        </x-dropdown-link>
                        @endif
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                        </form>
                        
                        <x-dropdown-link href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth

            <!-- Hamburger -->
            @auth
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @endauth
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if(Auth::check() && Auth::user()->role === 'admin')
            <x-responsive-nav-link :href="route('admin.surveys.index')" :active="request()->routeIs('admin.surveys.*')" class="nav-link">
                {{ __('Manajemen Survei') }}
            </x-responsive-nav-link>
            @endif
            @endauth

            @if(Auth::check() && Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="nav-link">
                    {{ __('Kategori') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.*')" class="nav-link">
                    {{ __('Pertanyaan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('survey-analysis.index')" :active="request()->routeIs('survey-analysis.*')" class="nav-link">
                    {{ __('Analisis Survey') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.backup.index')" :active="request()->routeIs('admin.backup.*')" class="nav-link">
                    {{ __('Backup Database') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.change-password.form')" :active="request()->routeIs('admin.change-password.*')" class="nav-link">
                    {{ __('Ganti Password') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('survey.index')" :active="request()->routeIs('survey.*')" class="nav-link">
                {{ __('Isi Survei') }}
            </x-responsive-nav-link>
            @if(Auth::guest() && (request()->routeIs('survey.*') || request()->routeIs('survey.guide')))
                <x-responsive-nav-link :href="route('survey.guide')" :active="request()->routeIs('survey.guide')">
                    {{ __('Panduan Pengisian') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <div class="text-xs text-gray-400">Role: {{ Auth::user()->role }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" id="logout-form-mobile">
                    @csrf
                </form>
                
                <x-responsive-nav-link href="#" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endauth
    </div>
</nav>
