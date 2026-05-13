<?php

require_once 'auth.php';
$adminName = $_SESSION['admin_nama'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Dashboard' ?> - SMRHJ</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @theme {
            --color-brand-navy: #0A2540;
            --color-brand-teal: #00BFA5;
            --color-brand-navy-light: #113a60;
            --color-brand-teal-dark: #009681;
            --font-sans: "Inter", sans-serif;
        }

        .nav-item {
            @apply flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors mb-1 font-medium text-sm;
        }
        .nav-item.active {
            @apply bg-brand-teal text-white shadow-md;
        }

        .modal { display: none; }
        .modal.active { display: flex; animation: modalFadeIn 0.3s ease-out forwards; }
        @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-gray-800">

    <header class="bg-white border-b border-gray-200 h-16 fixed top-0 right-0 left-0 lg:left-64 flex items-center justify-between px-4 sm:px-6 z-30 transition-all duration-300">
        <div class="flex items-center">
            <button id="sidebar-toggle" class="lg:hidden text-gray-500 hover:text-brand-navy focus:outline-none mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <h1 class="text-lg md:text-xl font-bold text-brand-navy"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
        </div>

        <div class="flex items-center space-x-4">
            <a href="../home.php" target="_blank" class="hidden sm:inline-flex items-center text-sm font-medium text-brand-teal hover:text-brand-navy transition-colors bg-brand-teal/10 px-3 py-1.5 rounded-md">
                Preview Web
            </a>

            <div class="flex items-center gap-3 border-l pl-4 border-gray-200">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-800 leading-tight"><?= htmlspecialchars($adminName) ?></p>
                    <p class="text-[10px] uppercase text-brand-teal font-semibold">Administrator</p>
                </div>
                <div class="w-9 h-9 bg-brand-navy rounded-full flex items-center justify-center text-white font-bold shadow-sm">A</div>
                <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')" class="ml-2 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>
    </header>
