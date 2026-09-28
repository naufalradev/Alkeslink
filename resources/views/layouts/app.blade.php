<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'AlkesLink') — Platform Hibah Alkes Layak Pakai</title>

    {{-- Vite: Tailwind CSS + JS bundle --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 font-sans antialiased">

{{-- ─── NAVIGATION ─── --}}
<nav class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-teal-600 text-lg">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                AlkesLink
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                <a href="{{ route('catalog.index') }}" class="hover:text-teal-600 transition">Katalog</a>
                @auth
                    @if(auth()->user()->isDonor())
                        <a href="{{ route('donor.items') }}" class="hover:text-teal-600 transition">Barang Saya</a>
                        <a href="{{ route('donor.requests') }}" class="hover:text-teal-600 transition">Permintaan</a>
                    @elseif(auth()->user()->isRecipient())
                        <a href="{{ route('recipient.requests') }}" class="hover:text-teal-600 transition">Pengajuan Saya</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-teal-600 transition">Admin</a>
                    @endif
                @endauth
            </div>

            {{-- CTA & auth --}}
            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}"
                       class="text-sm text-slate-600 hover:text-teal-600 font-medium transition hidden sm:block">Masuk</a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold transition">
                        Daftar
                    </a>
                @endguest
                @auth
                    @if(auth()->user()->isDonor())
                        <a href="{{ route('donor.items.create') }}"
                           class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold transition">
                            + Donasikan
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm text-slate-500 hover:text-red-500 transition font-medium">
                            Keluar
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ─── FLASH MESSAGES ─── --}}
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="fixed top-20 right-4 z-50 bg-green-500 text-white text-sm px-5 py-3 rounded-xl shadow-lg">
        {{ session('success') }}
    </div>
@endif

{{-- ─── PAGE CONTENT ─── --}}
<main>
    @yield('content')
</main>

{{-- ─── FOOTER ─── --}}
<footer class="bg-slate-800 text-slate-400 py-12 mt-16">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
            <p class="text-white font-bold text-lg mb-2">AlkesLink</p>
            <p class="text-sm leading-relaxed">Platform hibah alat kesehatan bekas layak pakai untuk masyarakat yang membutuhkan.</p>
        </div>
        <div>
            <p class="text-white font-semibold mb-3">Tautan</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition">Katalog Alkes</a></li>
                <li><a href="{{ route('home') }}#how-it-works" class="hover:text-white transition">Cara Kerja</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar Sebagai Donatur</a></li>
            </ul>
        </div>
        <div>
            <p class="text-white font-semibold mb-3">Kontak</p>
            <p class="text-sm">halo@alkeslink.id</p>
        </div>
    </div>
    <p class="text-center text-xs text-slate-600 mt-10">© {{ date('Y') }} AlkesLink. Dibuat dengan ❤️ untuk Indonesia.</p>
</footer>

{{-- ─── AI CHAT WIDGET ─── --}}
@include('components.ai-widget')

@stack('scripts')
</body>
</html>
