@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-teal-600">AlkesLink</a>
            <p class="text-slate-500 text-sm mt-2">Bergabung dan mulai berdampak!</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 space-y-5">
            <h1 class="text-xl font-bold text-slate-800">Buat Akun Baru</h1>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-600">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Saya mendaftar sebagai</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition
                                      has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 border-slate-200">
                            <input type="radio" name="role" value="donor"
                                   {{ old('role','recipient') === 'donor' ? 'checked' : '' }} class="accent-teal-600" />
                            <span class="text-sm font-medium">Donatur</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition
                                      has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 border-slate-200">
                            <input type="radio" name="role" value="recipient"
                                   {{ old('role','recipient') === 'recipient' ? 'checked' : '' }} class="accent-teal-600" />
                            <span class="text-sm font-medium">Penerima</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm transition">
                    Buat Akun
                </button>
            </form>

            <p class="text-center text-sm text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-teal-600 font-semibold hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
