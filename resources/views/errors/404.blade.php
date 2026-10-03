<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | {{ setting('site_short_name', 'SI-RT') }}</title>
    @include('layouts.tailwind-cdn')
    <link rel="icon" type="image/x-icon" href="{{ site_logo_url() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap">
    </noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </noscript>
    <style>
        body { font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif; }
        .error-code {
            background: linear-gradient(135deg, #007774 0%, #006663 60%, #005251 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }
        .float-slow { animation: float 5s ease-in-out infinite; }
        .float-slower { animation: float 7s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gray-50 antialiased min-h-screen">

    <div class="min-h-screen w-full flex flex-col lg:flex-row">

        {{-- Kiri: konten error --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 bg-gray-50 order-2 lg:order-1">
            <div class="w-full max-w-md text-center lg:text-left">

                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-200 bg-white px-4 py-1.5 shadow-sm mb-6">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                    <span class="text-xs font-bold tracking-widest text-gray-600 uppercase">Error 404</span>
                </div>

                {{-- Angka besar --}}
                <h1 class="error-code text-8xl sm:text-9xl font-black leading-none tracking-tight select-none">404</h1>

                <h2 class="mt-4 text-2xl sm:text-3xl font-extrabold text-gray-800">
                    Yah, halaman tidak ditemukan
                </h2>
                <p class="mt-3 text-gray-600 leading-relaxed">
                    Halaman yang Anda cari mungkin sudah dipindahkan, dihapus, atau URL yang
                    dimasukkan salah. Tenang, kami bantu Anda kembali ke jalan yang benar.
                </p>

                {{-- @if(request()->path() !== '/')
                    <p class="mt-3 inline-flex max-w-full items-center gap-2 rounded-lg bg-gray-100 border border-gray-200 px-3 py-2 text-xs text-gray-500">
                        <i class="fas fa-link text-gray-400 shrink-0"></i>
                        <span class="truncate font-mono">/{{ request()->path() }}</span>
                    </p>
                @endif --}}

                {{-- Tombol aksi --}}
                <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                    <button
                        type="button"
                        onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ url('/') }}'; }"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary-700 text-white font-bold px-6 py-3 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-primary/50 cursor-pointer">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </button>

                    @if(Route::has('dashboard'))
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border-2 border-gray-200 text-gray-700 font-bold px-6 py-3 shadow-sm hover:border-primary hover:text-primary transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-primary/20">
                            <i class="fas fa-home"></i>
                            Ke Dashboard
                        </a>
                    @else
                        <a href="{{ url('/') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border-2 border-gray-200 text-gray-700 font-bold px-6 py-3 shadow-sm hover:border-primary hover:text-primary transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-primary/20">
                            <i class="fas fa-home"></i>
                            Ke Beranda
                        </a>
                    @endif
                </div>

                {{-- Bantuan --}}
                <div class="mt-8 rounded-2xl bg-white border border-gray-200 shadow-sm p-4 flex items-start gap-3 text-left">
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <i class="fas fa-circle-question text-lg"></i>
                    </div>
                    <div class="text-sm">
                        <p class="font-bold text-gray-800">Butuh bantuan?</p>
                        <p class="text-gray-600 mt-0.5">
                            Jika Anda yakin alamat ini benar, silakan hubungi pengurus RT atau admin
                            {{ setting('site_short_name', 'SI-RT') }}.
                        </p>
                    </div>
                </div>

                <p class="mt-6 text-xs text-gray-400">
                    Kode error: 404 &bull; Halaman tidak ditemukan &bull; &copy; {{ date('Y') }} {{ setting('site_short_name', 'SI-RT') }}
                </p>
            </div>
        </div>

        {{-- Kanan: branding / ilustrasi --}}
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary to-primary-700 border-l border-primary-800 relative overflow-hidden order-1 lg:order-2 min-h-[320px]">
            {{-- Dekorasi --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-[28rem] h-[28rem] bg-white rounded-full translate-x-1/3 translate-y-1/3"></div>
                <div class="absolute top-1/2 left-1/2 w-72 h-72 bg-secondary rounded-full -translate-x-1/2 -translate-y-1/2 opacity-40"></div>
            </div>

            <div class="relative z-10 flex flex-col items-center justify-center w-full px-12 py-16 text-white">
                {{-- Logo --}}
                {{-- <div class="mb-6 bg-white rounded-3xl shadow-2xl p-5">
                    <img src="{{ site_logo_url() }}" alt="Logo" class="h-20 w-20 object-contain" />
                </div> --}}

                {{-- Ilustrasi rumah + kaca pembesar (SVG murni, tanpa aset eksternal) --}}
                <div class="float-slow mb-8">
                    <svg width="260" height="200" viewBox="0 0 260 200" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustrasi halaman tidak ditemukan">
                        {{-- Rumah --}}
                        <g opacity="0.95">
                            <rect x="40" y="90" width="110" height="80" rx="10" fill="white" fill-opacity="0.95"/>
                            <path d="M28 98 L95 45 L162 98" stroke="white" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect x="82" y="125" width="26" height="45" rx="4" fill="#007774"/>
                            <rect x="55" y="110" width="22" height="22" rx="4" fill="#007774" fill-opacity="0.25"/>
                            <rect x="113" y="110" width="22" height="22" rx="4" fill="#007774" fill-opacity="0.25"/>
                        </g>
                        {{-- Kaca pembesar dengan tanda tanya --}}
                        <g class="float-slower">
                            <circle cx="183" cy="128" r="42" fill="#f59e0b" fill-opacity="0.25"/>
                            <circle cx="183" cy="128" r="32" fill="white"/>
                            <circle cx="183" cy="128" r="32" stroke="#f59e0b" stroke-width="6"/>
                            <text x="183" y="143" text-anchor="middle" font-size="36" font-weight="900" fill="#007774" font-family="Nunito, sans-serif">?</text>
                            <rect x="205" y="150" width="12" height="34" rx="6" transform="rotate(-45 205 150)" fill="#f59e0b"/>
                        </g>
                        {{-- Awan kecil --}}
                        <ellipse cx="70" cy="42" rx="26" ry="12" fill="white" fill-opacity="0.5"/>
                        <ellipse cx="95" cy="36" rx="18" ry="10" fill="white" fill-opacity="0.5"/>
                        <ellipse cx="200" cy="40" rx="20" ry="9" fill="white" fill-opacity="0.35"/>
                    </svg>
                </div>

                <h1 class="text-4xl font-black mb-2 text-center">{{ setting('site_short_name', 'SI-RT') }}</h1>
                <p class="text-lg text-primary-200 text-center max-w-md">
                    {{ setting('site_name', 'Sistem Manajemen Rukun Tetangga') }}
                </p>
            </div>
        </div>

        {{-- Ilustrasi mobile (tampil di atas konten pada layar kecil) --}}
        <div class="lg:hidden order-1 bg-gradient-to-br from-primary to-primary-700 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-16 -left-16 w-64 h-64 bg-white rounded-full"></div>
                <div class="absolute -bottom-20 -right-16 w-72 h-72 bg-white rounded-full"></div>
            </div>
            <div class="relative z-10 flex items-center justify-center gap-3 px-6 py-8 text-white">
                <div class="bg-white rounded-2xl shadow-lg p-2.5">
                    <img src="{{ site_logo_url() }}" alt="Logo" class="h-10 w-10 object-contain" />
                </div>
                <div>
                    <p class="text-xl font-black leading-tight">{{ setting('site_short_name', 'SI-RT') }}</p>
                    <p class="text-xs text-primary-200">{{ setting('site_name', 'Sistem Manajemen Rukun Tetangga') }}</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
