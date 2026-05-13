<?php

$pageTitle = $pageTitle ?? 'Beranda';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($pageTitle) ?> - STIKES Mitra Ria Husada Jakarta</title>
    <meta name="description" content="Website Resmi Sekolah Tinggi Ilmu Kesehatan Mitra Ria Husada Jakarta (SMRHJ).">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-brand-navy: #0A2540;
            --color-brand-teal: #00BFA5;
            --font-sans: 'Inter', sans-serif;
            --font-heading: 'Outfit', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); }
    </style>
    <style>
        /* ===== RESPONSIVE DESIGN SYSTEM ===== */

        /* --- Mobile-first base resets --- */
        * { -webkit-tap-highlight-color: transparent; }
        img, video { max-width: 100%; height: auto; }
        input, select, textarea, button { font-size: 16px; } /* Prevent iOS zoom on focus */

        /* --- Smooth scroll for anchor links --- */
        @media (prefers-reduced-motion: no-preference) {
            html { scroll-behavior: smooth; }
        }

        /* --- Small mobile (up to 480px) --- */
        @media (max-width: 480px) {
            /* Reduce excessive vertical padding on sections */
            section { padding-top: 3rem !important; padding-bottom: 3rem !important; }

            /* Hero section height */
            #hero-carousel { min-height: 420px !important; height: 75vh !important; }

            /* Hero arrows - smaller and closer to edge */
            #hero-prev, #hero-next { width: 2.25rem !important; height: 2.25rem !important; }
            #hero-prev svg, #hero-next svg { width: 1rem !important; height: 1rem !important; }

            /* Navbar brand text */
            nav h1 { font-size: 0.95rem !important; line-height: 1.2 !important; }
            nav h1 + span { font-size: 0.6rem !important; }

            /* Container padding */
            .container { padding-left: 1rem !important; padding-right: 1rem !important; }

            /* Card padding reduction */
            .bg-white.rounded-3xl > .p-8,
            .bg-white.rounded-3xl > .p-10,
            .bg-brand-navy.rounded-3xl > .p-10 { padding: 1.25rem !important; }

            /* Footer grid */
            footer .grid { gap: 1.5rem !important; }
        }

        /* --- Mobile (up to 767px) --- */
        @media (max-width: 767px) {
            /* Section padding adjustments */
            section[class*="py-24"] { padding-top: 3.5rem !important; padding-bottom: 3.5rem !important; }
            section[class*="py-20"] { padding-top: 3rem !important; padding-bottom: 3rem !important; }
            div[class*="pb-24"] { padding-bottom: 3.5rem !important; }
            div[class*="pt-24"] { padding-top: 5rem !important; }
            div[class*="pb-20"] { padding-bottom: 3rem !important; }

            /* Section header margin-bottom */
            .text-center.mb-16 { margin-bottom: 2.5rem !important; }
            .text-center.mb-20 { margin-bottom: 2.5rem !important; }

            /* Typography scaling */
            h2[class*="text-4xl"], h2[class*="text-5xl"] { font-size: 1.75rem !important; }
            h3[class*="text-3xl"], h3[class*="text-4xl"] { font-size: 1.5rem !important; }
            h4[class*="text-2xl"] { font-size: 1.2rem !important; }

            /* Hero carousel adjustments */
            #hero-carousel { min-height: 450px !important; height: 80vh !important; }
            #hero-carousel h2 { font-size: 1.75rem !important; line-height: 1.2 !important; padding: 0 0.5rem; }
            #hero-carousel p { font-size: 0.95rem !important; }
            #hero-carousel a[class*="px-10"] { padding: 0.75rem 1.75rem !important; font-size: 0.95rem !important; }

            /* Decorative circles - prevent overflow */
            .absolute.-top-24, .absolute.-right-24,
            .absolute.-bottom-24, .absolute.-left-24 { display: none; }
            div[class*="w-[600px]"] { width: 300px !important; height: 300px !important; }

            /* Kerjasama section - smaller logos on mobile */
            .w-56.h-56 { width: 10rem !important; height: 10rem !important; }

            /* PMB Table - card layout on mobile */
            .pmb-table-wrapper table { display: block !important; }
            .pmb-table-wrapper thead { display: none !important; }
            .pmb-table-wrapper tbody { display: flex !important; flex-direction: column !important; gap: 1rem !important; }
            .pmb-table-wrapper tr {
                display: flex !important;
                flex-direction: column !important;
                background: white !important;
                border-radius: 1rem !important;
                padding: 1.25rem !important;
                border: 1px solid #e5e7eb !important;
                box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
            }
            .pmb-table-wrapper td {
                padding: 0.35rem 0 !important;
                text-align: left !important;
            }
            .pmb-table-wrapper td:first-child { font-size: 1.1rem !important; margin-bottom: 0.25rem !important; }
            .pmb-table-wrapper td:nth-child(2)::before { content: 'Periode: '; font-weight: 600; color: #0A2540; }
            .pmb-table-wrapper td:last-child { padding-top: 0.5rem !important; }

            /* Kontak page - reduce card padding on mobile */
            .bg-white.rounded-3xl.p-10,
            .bg-brand-navy.rounded-3xl.p-10 { padding: 1.5rem !important; }

            /* Alumni testimonial cards */
            .bg-slate-50.rounded-3xl.p-10 { padding: 1.5rem !important; }

            /* Form container padding */
            form.rounded-3xl .p-8 { padding: 1.25rem !important; }

            /* Submit buttons full-width on mobile */
            .flex.justify-end button[type="submit"] { width: 100% !important; }

            /* Navbar height */
            nav .h-20 { height: 4rem !important; }

            /* Mobile menu improvements */
            #mobile-menu {
                max-height: 80vh;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Breadcrumb text */
            .flex.items-center.justify-center.text-sm { font-size: 0.8rem !important; }

            /* Berita detail share section */
            .flex.flex-col.sm\:flex-row { flex-direction: column !important; }

            /* Modal adjustments */
            #prodi-modal > div, #pmb-modal > div { margin: 0.5rem !important; max-height: 90vh; overflow-y: auto; }

            /* Pimpinan cards - avatar size */
            .w-32.h-32 { width: 6rem !important; height: 6rem !important; }

            /* Contact info items - icon size */
            .w-14.h-14 { width: 2.75rem !important; height: 2.75rem !important; }
            .w-14.h-14 svg { width: 1.25rem !important; height: 1.25rem !important; }
            .w-14.h-14 + div h4 { font-size: 1rem !important; }

            /* Footer bottom bar */
            footer .border-t .flex { flex-direction: column !important; text-align: center; gap: 0.75rem !important; }
        }

        /* --- Tablet (768px to 1023px) --- */
        @media (min-width: 768px) and (max-width: 1023px) {
            /* Moderate section padding */
            section[class*="py-24"] { padding-top: 4rem !important; padding-bottom: 4rem !important; }

            /* Kerjasama logos */
            .w-56.h-56 { width: 12rem !important; height: 12rem !important; }

            /* PMB table readability */
            .pmb-table-wrapper th, .pmb-table-wrapper td { padding-left: 1.25rem !important; padding-right: 1.25rem !important; }
        }

        /* --- Mobile menu slide animation --- */
        #mobile-menu {
            transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
        }
        #mobile-menu.hidden {
            max-height: 0 !important;
            opacity: 0;
            overflow: hidden;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        #mobile-menu:not(.hidden) {
            max-height: 80vh;
            opacity: 1;
        }

        /* --- Touch-friendly interactive elements --- */
        @media (hover: none) and (pointer: coarse) {
            a, button { min-height: 44px; min-width: 44px; }
            nav a { padding-top: 0.625rem !important; padding-bottom: 0.625rem !important; }
        }

        /* --- Safe area support for notched devices --- */
        @supports (padding: max(0px)) {
            body {
                padding-left: env(safe-area-inset-left);
                padding-right: env(safe-area-inset-right);
            }
            nav {
                padding-left: max(1rem, env(safe-area-inset-left));
                padding-right: max(1rem, env(safe-area-inset-right));
            }
        }

        /* --- Prevent horizontal scroll --- */
        html, body { overflow-x: hidden; max-width: 100vw; }
    </style>
</head>
<body class="font-sans text-slate-700 bg-slate-50 antialiased flex flex-col min-h-screen">
