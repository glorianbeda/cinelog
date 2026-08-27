<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inisiasi Pemilik Website — CineLog Setup</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0D0D12] text-zinc-100 flex items-center justify-center p-4 selection:bg-purple-500 selection:text-white">
    <div class="max-w-2xl w-full my-8">
        <!-- Header Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-amber-400 items-center justify-center border-3 border-black shadow-[4px_4px_0px_0px_#A855F7] mb-4">
                <x-lucide-film class="w-8 h-8 text-black" />
            </div>
            <h1 class="text-3xl font-black font-mono tracking-tight text-white">
                SETUP <span class="text-amber-400">CINELOG</span>
            </h1>
            <p class="text-sm text-zinc-400 mt-2 font-mono">
                Konfigurasi Satu Kali Identitas Pemilik & Akun Pengelola
            </p>
        </div>

        <!-- Info Alert -->
        <div class="mb-6 p-4 bg-purple-950/40 border-2 border-purple-500 rounded-xl text-purple-200 text-xs font-mono shadow-[4px_4px_0px_0px_#A855F7]">
            <div class="flex items-start gap-3">
                <x-lucide-shield-alert class="w-5 h-5 text-purple-400 shrink-0 mt-0.5" />
                <div>
                    <strong class="font-bold text-white block mb-1">One-Time Setup Lock</strong>
                    Nama dan profil yang Anda masukkan di bawah akan menjadi identitas kurator di halaman depan publik. Begitu form ini disimpan, pendaftaran akan terkunci permanen untuk keamanan solo-admin Anda.
                </div>
            </div>
        </div>

        <x-flash-messages />

        <!-- Setup Form Card -->
        <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-6 sm:p-8 shadow-[8px_8px_0px_0px_#F59E0B]">
            <form action="{{ route('setup.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Section: Identitas Kurator -->
                <div class="space-y-4">
                    <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                        <x-lucide-user class="w-4 h-4" />
                        <span>Identitas Pemilik (Ditampilkan di Publik)</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div>
                            <label for="name" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                                Nama Lengkap / Display Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   placeholder="Contoh: Alex Pratama"
                                   class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm font-medium">
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                                Username / Handle <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   placeholder="contoh: alexcinephile"
                                   class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm font-mono">
                        </div>
                    </div>

                    <!-- Bio / Tagline Kurator -->
                    <div>
                        <label for="bio" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                            Bio / Tagline Singkat Kurator
                        </label>
                        <textarea id="bio" 
                                  name="bio" 
                                  rows="2" 
                                  placeholder="Contoh: Penikmat film Sci-Fi & Psychological Thriller. Mendokumentasikan ulasan film dan series favorit."
                                  class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm">{{ old('bio') }}</textarea>
                    </div>

                    <!-- Avatar URL -->
                    <div>
                        <label for="avatar_url" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                            URL Foto Avatar (Opsional)
                        </label>
                        <input type="url" 
                               id="avatar_url" 
                               name="avatar_url" 
                               value="{{ old('avatar_url') }}" 
                               placeholder="https://images.unsplash.com/... atau URL foto profil Anda"
                               class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm font-mono">
                    </div>
                </div>

                <!-- Section: Kredensial Login Admin -->
                <div class="space-y-4 pt-4 border-t border-slate-800">
                    <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-purple-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                        <x-lucide-lock class="w-4 h-4" />
                        <span>Kredensial Login Admin</span>
                    </h2>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                            Email Pengelola <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="admin@domain.com"
                               class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm font-mono">
                    </div>

                    <!-- Password & Confirm -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                                Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Minimal 6 karakter"
                                   class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm font-mono">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                                Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   placeholder="Ulangi kata sandi"
                                   class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm font-mono">
                        </div>
                    </div>
                </div>

                <!-- Section: Integrasi TMDB API -->
                <div class="space-y-4 pt-4 border-t border-slate-800">
                    <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                        <x-lucide-database class="w-4 h-4" />
                        <span>Kunci Open API (TMDB)</span>
                    </h2>

                    <div>
                        <label for="tmdb_api_key" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                            TMDB API Key v3 (Opsional, bisa diisi nanti di settings)
                        </label>
                        <input type="text" 
                               id="tmdb_api_key" 
                               name="tmdb_api_key" 
                               value="{{ old('tmdb_api_key') }}" 
                               placeholder="Contoh: 3b8c459f..."
                               class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm font-mono">
                        <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                            Dapatkan API Key gratis di <a href="https://www.themoviedb.org/settings/api" target="_blank" class="text-cyan-400 underline">themoviedb.org</a> untuk fitur auto-fetch poster dan sinopsis 1-klik.
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full neo-btn py-3.5 px-6 rounded-xl bg-amber-400 hover:bg-amber-300 text-black text-base font-black font-mono shadow-[4px_4px_0px_0px_#A855F7]">
                        <x-lucide-check-circle-2 class="w-5 h-5 mr-2" />
                        <span>Selesaikan Inisiasi & Buka Panel Admin</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
