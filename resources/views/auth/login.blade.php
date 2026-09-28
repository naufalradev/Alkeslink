@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-teal-600">AlkesLink</a>
            <p class="text-slate-500 text-sm mt-2">Selamat datang kembali!</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 space-y-5">
            <h1 class="text-xl font-bold text-slate-800">Masuk ke Akun</h1>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="rounded" />
                    <label for="remember" class="text-sm text-slate-500">Ingat saya</label>
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm transition">
                    Masuk
                </button>
            </form>

            <p class="text-center text-sm text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-teal-600 font-semibold hover:underline">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
