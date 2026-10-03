{{--
    Pengganti @vite: Tailwind via CDN + JS statis.
    Dipakai agar production tidak wajib `npm run build` setiap ada class baru —
    Play CDN meng-compile class saat halaman dibuka (termasuk class yang
    muncul dinamis via Alpine/JS).

    Token warna & font disamakan dengan resources/css/app.css.
--}}

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Nunito', 'ui-sans-serif', 'system-ui', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
                },
                colors: {
                    /* Palet "primary / secondary" ala referensi D-ASSA */
                    primary: {
                        DEFAULT: '#007774',
                        200: '#a7d8d6',
                        600: '#00706d',
                        700: '#006663',
                        800: '#005251',
                    },
                    secondary: {
                        DEFAULT: '#f59e0b',
                        200: '#fde68a',
                    },
                    /* Simmutu theme tokens */
                    sp: {
                        primary: '#007774',
                        'primary-dark': '#006663',
                        navy: '#25396f',
                        icon: '#7c8db5',
                        hover: 'rgba(37, 57, 111, 0.05)',
                        'submenu-hover': '#f0f1f5',
                    },
                },
                boxShadow: {
                    'sp-active': '0 4px 12px rgba(0, 119, 116, 0.2)',
                },
                maxWidth: {
                    '40': '10rem',
                },
            },
        },
    };
</script>

<style type="text/tailwindcss">
    /* Compact scale yang sama dengan sidebar Simmutu (1rem = 12px) */
    html {
        font-size: 75%;
    }

    @layer components {
        /* Brand di atas sidebar hijau */
        .sidebar-brand-text {
            @apply text-[1.7rem] font-bold text-white;
        }

        /* Group titles */
        .sidebar-title {
            @apply px-4 mt-6 mb-4 text-[1rem] font-semibold text-primary-200;
        }

        /* Main links & dropdown buttons (teks putih di atas hijau) */
        .sidebar-link,
        .sidebar-btn {
            @apply flex items-center gap-3 py-3 px-4 text-white/85 text-[0.95rem] font-semibold rounded-lg transition-all duration-150;
        }

        .sidebar-btn {
            @apply font-sans text-left;
        }

        /* Icon box (Simmutu: 24px box, 1.2rem glyph) */
        .sidebar-icon {
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-link i,
        .sidebar-btn i {
            @apply text-white/70 text-[1.2rem] leading-none transition-all duration-150;
        }

        .sidebar-link:hover,
        .sidebar-btn:hover {
            @apply bg-white/10 text-white;
        }

        .sidebar-link:hover i,
        .sidebar-btn:hover i {
            @apply text-secondary-200 scale-110;
        }

        .sidebar-link.active,
        .sidebar-btn.active {
            @apply bg-white text-primary shadow-md;
        }

        .sidebar-link.active i,
        .sidebar-btn.active i {
            @apply text-primary;
        }

        /* Submenu (Simmutu: 0.6rem 1.7rem, 0.85rem) */
        .sidebar-submenu a {
            @apply flex items-center gap-3 px-[1.7rem] py-[0.6rem] my-[0.2rem] text-white/80 text-[0.85rem] font-semibold tracking-wide rounded-lg transition-all duration-200;
        }

        .sidebar-submenu a i {
            @apply text-white/60 text-[1rem] transition-colors duration-200;
        }

        .sidebar-submenu a:hover {
            @apply bg-white/10 text-white;
        }

        .sidebar-submenu a:hover i {
            @apply text-secondary-200;
        }

        .sidebar-submenu a.active {
            @apply bg-white text-primary;
        }

        .sidebar-submenu a.active i {
            @apply text-primary;
        }

        /* Chevron indicator */
        .sidebar-chevron {
            @apply text-[0.7rem] text-white/60;
        }

        .sidebar-link:hover .sidebar-chevron,
        .sidebar-btn:hover .sidebar-chevron {
            @apply text-secondary-200;
        }
    }
</style>

{{-- Axios + registrasi Alpine.data (pengganti resources/js/app.js hasil build) --}}
<script src="https://cdn.jsdelivr.net/npm/axios@1.11.0/dist/axios.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
