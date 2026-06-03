<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Info Desa') - {{ $villageInfo->village_name ?? 'Desa' }}</title>
    
    <!-- Meta Tags -->
    @if($villageInfo && $villageInfo->meta_title)
        <meta name="description" content="{{ $villageInfo->meta_description }}">
        <meta name="keywords" content="{{ $villageInfo->meta_keywords }}">
    @endif
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        accent: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <!-- AOS CSS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .font-display {
            font-family: 'Playfair Display', serif;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #1e40af 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #1e3a5f 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-50 scroll-smooth">
    <!-- Top Bar -->
    <div class="bg-primary-900 text-white text-sm py-2">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap justify-between items-center">
            <div class="flex items-center space-x-4">
                <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $villageInfo->address ?? '' }}</span>
                @if($villageInfo && $villageInfo->phone)
                    <span class="hidden sm:inline"><i class="fas fa-phone mr-1"></i> {{ $villageInfo->phone }}</span>
                @endif
            </div>
            <div class="flex items-center space-x-3">
                @if($villageInfo && $villageInfo->facebook)
                    <a href="{{ $villageInfo->facebook }}" target="_blank" class="hover:text-accent-400 transition"><i class="fab fa-facebook-f"></i></a>
                @endif
                @if($villageInfo && $villageInfo->instagram)
                    <a href="{{ $villageInfo->instagram }}" target="_blank" class="hover:text-accent-400 transition"><i class="fab fa-instagram"></i></a>
                @endif
                @if($villageInfo && $villageInfo->youtube)
                    <a href="{{ $villageInfo->youtube }}" target="_blank" class="hover:text-accent-400 transition"><i class="fab fa-youtube"></i></a>
                @endif
                @if($villageInfo && $villageInfo->tiktok)
                    <a href="{{ $villageInfo->tiktok }}" target="_blank" class="hover:text-accent-400 transition"><i class="fab fa-tiktok"></i></a>
                @endif
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3">
                    @if($villageInfo && $villageInfo->logo_path)
                        <img src="{{ asset('storage/' . $villageInfo->logo_path) }}" alt="Logo" class="h-12 w-12 object-contain">
                    @else
                        <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-home text-white text-xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="font-bold text-xl text-primary-800">{{ $villageInfo->village_name ?? 'Desa' }}</h1>
                        <p class="text-xs text-gray-500">{{ $villageInfo->district_name ?? '' }}, {{ $villageInfo->regency_name ?? '' }}</p>
                    </div>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-1">
                    <a href="/" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">Beranda</a>
                    <a href="/profil-desa" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">Profil Desa</a>
                    <a href="/bumdes" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">BUMDes</a>
                    <a href="/unit-usaha" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">Unit Usaha</a>
                    <a href="/potensi" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">Potensi</a>
                    <a href="/berita" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">Berita</a>
                    <a href="/galeri" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">Galeri</a>
                    <a href="/kontak" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">Kontak</a>
                    <a href="/admin" class="ml-4 px-5 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium">
                        <i class="fas fa-lock mr-1"></i> Admin
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <div class="lg:hidden">
                    <button id="mobile-menu-btn" class="text-gray-700 hover:text-primary-600 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t">
            <div class="px-4 py-3 space-y-1">
                <a href="/" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">
                    <i class="fas fa-home mr-3 w-5"></i> Beranda
                </a>
                <a href="/profil-desa" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">
                    <i class="fas fa-building mr-3 w-5"></i> Profil Desa
                </a>
                <a href="/bumdes" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">
                    <i class="fas fa-store mr-3 w-5"></i> BUMDes
                </a>
                <a href="/unit-usaha" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">
                    <i class="fas fa-building mr-3 w-5"></i> Unit Usaha
                </a>
                <a href="/potensi" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">
                    <i class="fas fa-mountain mr-3 w-5"></i> Potensi
                </a>
                <a href="/berita" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">
                    <i class="fas fa-newspaper mr-3 w-5"></i> Berita
                </a>
                <a href="/galeri" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">
                    <i class="fas fa-images mr-3 w-5"></i> Galeri
                </a>
                <a href="/kontak" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">
                    <i class="fas fa-envelope mr-3 w-5"></i> Kontak
                </a>
                <a href="/admin" class="block px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium text-center">
                    <i class="fas fa-lock mr-2"></i> Admin Panel
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-primary-900 text-white">
        <!-- Main Footer -->
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        @if($villageInfo && $villageInfo->logo_path)
                            <img src="{{ asset('storage/' . $villageInfo->logo_path) }}" alt="Logo" class="h-10 w-10 object-contain">
                        @else
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-home text-white"></i>
                            </div>
                        @endif
                        <h3 class="font-bold text-lg">{{ $villageInfo->village_name ?? 'Desa' }}</h3>
                    </div>
                    <p class="text-sm text-gray-300 leading-relaxed">
                        {{ Str::limit($villageInfo->about_village ?? 'Website resmi desa.', 150) }}
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold text-lg mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/" class="text-gray-300 hover:text-accent-400 transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>Beranda</a></li>
                        <li><a href="/profil-desa" class="text-gray-300 hover:text-accent-400 transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>Profil Desa</a></li>
                        <li><a href="/bumdes" class="text-gray-300 hover:text-accent-400 transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>BUMDes</a></li>
                        <li><a href="/potensi" class="text-gray-300 hover:text-accent-400 transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>Potensi Desa</a></li>
                        <li><a href="/berita" class="text-gray-300 hover:text-accent-400 transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>Berita</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h4 class="font-bold text-lg mb-4">Kontak Kami</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt mt-1 text-accent-400"></i>
                            <span class="text-gray-300">{{ $villageInfo->address ?? '' }}</span>
                        </li>
                        @if($villageInfo && $villageInfo->phone)
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-phone text-accent-400"></i>
                                <span class="text-gray-300">{{ $villageInfo->phone }}</span>
                            </li>
                        @endif
                        @if($villageInfo && $villageInfo->email)
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-envelope text-accent-400"></i>
                                <span class="text-gray-300">{{ $villageInfo->email }}</span>
                            </li>
                        @endif
                        @if($villageInfo && $villageInfo->contact_whatsapp)
                            <li class="flex items-center space-x-3">
                                <i class="fab fa-whatsapp text-accent-400"></i>
                                <span class="text-gray-300">{{ $villageInfo->contact_whatsapp }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <!-- Social Media -->
                <div>
                    <h4 class="font-bold text-lg mb-4">Ikuti Kami</h4>
                    <div class="flex flex-wrap gap-3">
                        @if($villageInfo && $villageInfo->facebook)
                            <a href="{{ $villageInfo->facebook }}" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-blue-600 transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if($villageInfo && $villageInfo->instagram)
                            <a href="{{ $villageInfo->instagram }}" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-pink-600 transition">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if($villageInfo && $villageInfo->youtube)
                            <a href="{{ $villageInfo->youtube }}" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-red-600 transition">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                        @if($villageInfo && $villageInfo->tiktok)
                            <a href="{{ $villageInfo->tiktok }}" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-black transition">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        @endif
                        @if($villageInfo && $villageInfo->twitter)
                            <a href="{{ $villageInfo->twitter }}" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-blue-400 transition">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Footer -->
        <div class="border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ $villageInfo->village_name ?? 'Desa' }}. Hak Cipta Dilindungi.</p>
                    <p class="mt-2 md:mt-0">
                        {{ $villageInfo->district_name ?? '' }}, {{ $villageInfo->regency_name ?? '' }}, {{ $villageInfo->province_name ?? '' }}
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    @if($villageInfo && $villageInfo->contact_whatsapp)
        <a href="https://wa.me/{{ $villageInfo->contact_whatsapp }}" target="_blank" 
           class="fixed bottom-6 right-6 w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition z-50 animate-bounce">
            <i class="fab fa-whatsapp text-white text-2xl"></i>
        </a>
    @endif

    <!-- Back to Top -->
    <button id="back-to-top" class="fixed bottom-6 left-6 w-12 h-12 bg-primary-600 rounded-full items-center justify-center shadow-lg hover:bg-primary-700 transition z-50 hidden">
        <i class="fas fa-arrow-up text-white"></i>
    </button>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- AOS JS (Animate On Scroll) -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
        
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        
        // Back to top button
        const backToTopBtn = document.getElementById('back-to-top');
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.remove('hidden');
                backToTopBtn.classList.add('flex');
            } else {
                backToTopBtn.classList.add('hidden');
                backToTopBtn.classList.remove('flex');
            }
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Lazy loading for images
        if ('loading' in HTMLImageElement.prototype) {
            // Browser supports native lazy loading
            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                img.src = img.dataset.src;
            });
        } else {
            // Fallback for browsers that don't support native lazy loading
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        observer.unobserve(img);
                    }
                });
            });
            lazyImages.forEach(img => imageObserver.observe(img));
        }
    </script>
    @yield('scripts')
</body>
</html>
