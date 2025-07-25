<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $seoSettings = \App\Models\Setting::getByType('seo');
        $settings = \App\Models\Setting::getByType('general');
        $primaryColor = $settings['primary_color'] ?? '#193a66';
        $secondaryColor = $settings['secondary_color'] ?? '#f8c102';
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seoSettings['meta_title'] ?? 'PERI - Maximum Efficiency' }}</title>
    <meta name="description"
        content="{{ $seoSettings['meta_description'] ?? 'Discover the power of efficient project management with our cutting-edge Project Management Software. Streamline tasks, boost collaboration, and achieve your project goals seamlessly. ' }}">
    <meta name="keywords"
        content="{{ $seoSettings['meta_keywords'] ?? 'Project Management System , PMS , AMS, Management System, Attendance Management System , Management Software , Project Tracking Software , Mobipixels PMS , Mobipixels , PMS Mobipixels' }}">

    <!-- Tailwind & Alpine.js (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireStyles
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('storage/' . $settings['favicon']) }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/front.css') }}">

    <style>
        :root {
            --primary: {{ $primaryColor }};
            --secondary: {{ $secondaryColor }};
        }
    </style>
</head>

<body>
    <!-- Navbar Section -->
    <nav class="bg-white shadow-md fixed top-0 w-full z-50" x-data="{ open: false }">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo (Left) -->
                <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">
                    <img src="{{ asset('storage/' . $settings['main_logo']) }}" alt="Logo" style="height: 3.5rem;">
                </a>

                <!-- Mobile Toggle Button -->
                <button @click="open = !open" class="lg:hidden text-gray-800 focus:outline-none">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16 M4 12h16 M4 18h16"></path>
                    </svg>

                    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>

                <!-- Navbar Links (Center, Visible on Large Screens) -->
                <div class="hidden lg:flex space-x-6">
                    <a href="{{ url('/#features') }}" class="nav-link">Features</a>
                    <a href="{{ url('/#pricing-table') }}" class="nav-link">Pricing</a>
                    <a href="{{ url('/#contact') }}" class="nav-link">Contact</a>
                    <a href="{{ url('/guide') }}" class="nav-link">Guide</a>
                    <a href="{{ url('/client/login') }}" class="nav-link">Login</a>
                </div>

                <!-- Login Button (Right) -->
                <a href="{{ url('/client/register') }}"
                    class="hidden lg:inline-block px-4 py-2 text-[var(--primary)] bg-[var(--secondary)] font-semibold rounded
                                  transition-all duration-300 border-0
                                  hover:bg-[var(--primary)] hover:text-white">
                    Try For Free
                </a>
            </div>

            <!-- Mobile Menu (Collapsible) -->
            <div x-show="open" class="lg:hidden mt-2 bg-white shadow-md rounded-lg p-4 space-y-2" x-transition>
                <!-- Menu Links -->
                <nav class="space-y-1">
                    <a href="{{ url('/#features') }}" class="block py-2 nav-link">Features</a>
                    <a href="{{ url('/#pricing-table') }}" class="block py-2 nav-link">Pricing</a>
                    <a href="{{ url('/#contact') }}" class="block py-2 nav-link">Contact</a>
                    <a href="{{ url('/guide') }}" class="block py-2 nav-link">Guide</a>
                    <a href="{{ url('/client/login') }}" class="block py-2 nav-link">Login</a>
                </nav>

                <!-- CTA Button -->
                <a href="{{ url('/client/register') }}"
                    class="block mt-2 px-4 py-2 text-[var(--primary)] bg-[var(--secondary)] 
              font-semibold rounded-lg transition-all duration-300 border-0 
              hover:bg-[var(--primary)] hover:text-white text-center">
                    Try For Free
                </a>
            </div>

        </div>
    </nav>

    @yield('content')

    <!-- Footer Section -->
    <footer class="bg-gray-100 text-gray-800  py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Contact Section -->
                <div>
                    <h5 class="text-lg font-bold mb-4">Contact Us</h5>
                    <p class="flex items-center mb-3">
                        <span
                            class="bg-[var(--secondary)] text-gray-900 p-2 rounded-full flex items-center justify-center w-10 h-10">
                            <i class="bi bi-telephone-fill"></i>
                        </span>
                        <span class="ml-3">051-6102534</span>
                    </p>
                    <p class="flex items-center">
                        <span
                            class="bg-[var(--secondary)] text-gray-900 p-2 rounded-full flex items-center justify-center w-10 h-10">
                            <i class="bi bi-envelope-fill"></i>
                        </span>
                        <span class="ml-3">ag.rana@airnet-technologies.com</span>
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h5 class="text-lg font-bold mb-4">Quick Links</h5>
                    <div class="grid grid-cols-2 gap-2 text-gray-300">
                        <a href="{{ url('/') }}"
                            class="text-gray-900 hover:text-[var(--secondary)] transition duration-200">Home</a>
                        <a href="{{ url('/#about') }}"
                            class="text-gray-900 hover:text-[var(--secondary)] transition duration-200">About</a>
                        <a href="{{ url('/#pricing-table') }}"
                            class="text-gray-900 hover:text-[var(--secondary)] transition duration-200">Pricing</a>
                        <a href="{{ url('/guide') }}"
                            class="text-gray-900 hover:text-[var(--secondary)] transition duration-200">Guide</a>
                        <a href="{{ url('/#contact') }}"
                            class="text-gray-900 hover:text-[var(--secondary)] transition duration-200">Contact</a>
                        <a href="#"
                            class="text-gray-900 hover:text-[var(--secondary)] transition duration-200">Team
                            Members</a>
                        <a href="{{ url('/terms-conditions') }}"
                            class="text-gray-900 hover:text-[var(--secondary)] transition duration-200">Terms &
                            Conditions</a>
                        <a href="{{ url('/privacy-policy') }}"
                            class="text-gray-900 hover:text-[var(--secondary)] transition duration-200">Privacy
                            Policy</a>
                    </div>
                </div>

                <!-- Social Icons & Logo -->
                <div class="flex flex-col items-center text-center w-full">
                    <img src="{{ asset('storage/' . $settings['main_logo']) }}" alt="Logo"
                        class="w-28 sm:w-32 md:w-40 lg:w-50 mb-4">

                    <div
                        class="flex flex-row flex-nowrap justify-center gap-3 sm:gap-4 
                max-w-xs sm:max-w-sm mx-auto overflow-hidden">
                        <a href="https://www.facebook.com/MobiPixels"
                            class="bg-[var(--secondary)] text-gray-900 p-3 rounded-full 
            flex items-center justify-center w-12 h-12 
            hover:bg-[var(--primary)] hover:text-white transition duration-300">
                            <i class="bi bi-facebook text-xl"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/mobipixels"
                            class="bg-[var(--secondary)] text-gray-900 p-3 rounded-full 
            flex items-center justify-center w-12 h-12 
            hover:bg-[var(--primary)] hover:text-white transition duration-300">
                            <i class="bi bi-linkedin text-xl"></i>
                        </a>
                        <a href="https://www.youtube.com/@mobipixels4547"
                            class="bg-[var(--secondary)] text-gray-900 p-3 rounded-full 
            flex items-center justify-center w-12 h-12 
            hover:bg-[var(--primary)] hover:text-white transition duration-300">
                            <i class="bi bi-youtube text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-gray-700 mt-8 pt-4 text-center text-gray-400">
                <p class="text-sm">&copy; {{ date('Y') }} {{ $settings['footer_text'] }}
                </p>
            </div>
        </div>
    </footer>
    <script src="https://www.google.com/recaptcha/api.js?render={{ $ReCaptcha['site_key'] }}"></script>
    @livewireScripts
</body>

</html>
