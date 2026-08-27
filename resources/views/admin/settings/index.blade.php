@extends('layouts.admin')

@section('title', 'Pengaturan Profil & Sistem')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-black font-mono tracking-tight text-white flex items-center gap-2">
            <x-lucide-settings class="w-6 h-6 text-purple-400" />
            <span>Pengaturan Profil & Sistem</span>
        </h1>
        <p class="text-xs text-zinc-400 font-mono mt-0.5">
            Perbarui nama kurator, bio, kunci API TMDB, dan kata sandi akun pengelola Anda.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Left: Profile Summary Card -->
        <div class="space-y-6">
            <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-6 shadow-[6px_6px_0px_0px_#A855F7] text-center space-y-4 font-mono">
                <!-- Avatar Preview -->
                <div class="w-24 h-24 mx-auto rounded-2xl bg-amber-400 border-3 border-black shadow-[4px_4px_0px_0px_#fff] overflow-hidden flex items-center justify-center">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <x-lucide-user class="w-12 h-12 text-black" />
                    @endif
                </div>

                <div>
                    <h3 class="font-bold text-white text-base">{{ $user->name }}</h3>
                    <span class="text-xs text-purple-400 font-bold block">{{ '@' . $user->username }}</span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">{{ $user->email }}</span>
                </div>

                <div class="pt-3 border-t border-slate-800 text-left">
                    <span class="text-[11px] text-zinc-500 font-bold uppercase block mb-1">Bio Publik:</span>
                    <p class="text-xs text-zinc-300 font-sans italic">{{ $user->bio ?? 'Belum ada bio.' }}</p>
                </div>
            </div>

            <!-- TMDB API Key Guide Card -->
            <div class="bg-[#14141E] border-2 border-slate-800 rounded-2xl p-5 space-y-2 font-mono text-xs text-zinc-400">
                <div class="flex items-center gap-1.5 text-cyan-400 font-bold">
                    <x-lucide-info class="w-4 h-4" />
                    <span>Panduan TMDB API</span>
                </div>
                <p class="text-[11px] leading-relaxed">
                    Kunci API digunakan oleh server untuk mengambil judul, poster, backdrop, dan sinopsis secara otomatis saat Anda menulis ulasan.
                </p>
                <a href="https://www.themoviedb.org/settings/api" target="_blank" class="inline-flex items-center gap-1 text-cyan-400 hover:underline text-xs font-bold pt-1">
                    <span>Buka themoviedb.org/settings/api</span>
                    <x-lucide-external-link class="w-3 h-3" />
                </a>
            </div>
        </div>

        <!-- Right: Forms Column -->
        <div class="md:col-span-2 space-y-8">
            
            <!-- FORM 1: Update Profile & TMDB API Key -->
            <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-6 sm:p-8 shadow-[6px_6px_0px_0px_#F59E0B] space-y-6">
                <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                    <x-lucide-user-check class="w-4 h-4" />
                    <span>Identitas Pemilik Website</span>
                </h2>

                <form action="{{ route('admin.settings.profile') }}" method="POST" class="space-y-4 font-mono text-xs">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-bold text-zinc-200 mb-1">
                                Nama Lengkap Kurator <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-sm font-sans font-bold">
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block font-bold text-zinc-200 mb-1">
                                Username / Handle <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', $user->username) }}" 
                                   required 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block font-bold text-zinc-200 mb-1">
                            Email Akun Admin <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block font-bold text-zinc-200 mb-1">
                            Bio / Tagline Kurator (Tampil di Beranda)
                        </label>
                        <textarea id="bio" 
                                  name="bio" 
                                  rows="3" 
                                  class="w-full neo-input px-3.5 py-2 rounded-lg text-xs font-sans">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <!-- Avatar URL -->
                    <div>
                        <label for="avatar_url" class="block font-bold text-zinc-200 mb-1">
                            URL Foto Avatar
                        </label>
                        <input type="url" 
                               id="avatar_url" 
                               name="avatar_url" 
                               value="{{ old('avatar_url', $user->avatar_url) }}" 
                               placeholder="https://..."
                               class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                    </div>

                    <!-- TMDB API Key -->
                    <div class="pt-2 border-t border-slate-800">
                        <label for="tmdb_api_key" class="block font-bold text-cyan-400 mb-1">
                            TMDB API Key v3
                        </label>
                        <input type="text" 
                               id="tmdb_api_key" 
                               name="tmdb_api_key" 
                               value="{{ old('tmdb_api_key', $user->tmdb_api_key) }}" 
                               placeholder="Masukkan API Key TMDB v3..."
                               class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                    </div>

                    <div class="pt-3">
                        <button type="submit" 
                                class="neo-btn px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-black text-xs font-bold font-mono shadow-[3px_3px_0px_0px_#fff]">
                            <x-lucide-check class="w-4 h-4 mr-1.5" />
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- FORM 2: Change Password -->
            <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-6 sm:p-8 shadow-[6px_6px_0px_0px_#A855F7] space-y-6">
                <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-purple-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                    <x-lucide-lock class="w-4 h-4" />
                    <span>Ganti Kata Sandi</span>
                </h2>

                <form action="{{ route('admin.settings.password') }}" method="POST" class="space-y-4 font-mono text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block font-bold text-zinc-200 mb-1">
                            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               required 
                               class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block font-bold text-zinc-200 mb-1">
                                Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Minimal 6 karakter"
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block font-bold text-zinc-200 mb-1">
                                Ulangi Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" 
                                class="neo-btn px-6 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-black text-xs font-bold font-mono shadow-[3px_3px_0px_0px_#fff]">
                            <x-lucide-key class="w-4 h-4 mr-1.5" />
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
