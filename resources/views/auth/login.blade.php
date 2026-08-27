<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Pengelola — CineLog</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0D0D12] text-zinc-100 flex items-center justify-center p-4 selection:bg-purple-500 selection:text-white">
    <div class="max-w-md w-full my-8">
        <!-- Header Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-purple-500 items-center justify-center border-2 border-black shadow-[4px_4px_0px_0px_#F59E0B] mb-4">
                <x-lucide-shield class="w-7 h-7 text-black" />
            </div>
            <h1 class="text-2xl font-black font-mono tracking-tight text-white">
                CINELOG <span class="text-purple-400">MANAGEMENT</span>
            </h1>
            <p class="text-xs text-zinc-400 mt-1 font-mono">
                Akses Khusus Pengelola & Pemilik
            </p>
        </div>

        <x-flash-messages />

        <!-- Login Form Card -->
        <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-6 sm:p-8 shadow-[6px_6px_0px_0px_#A855F7]">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                        Email Admin
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
                            <x-lucide-mail class="w-4 h-4" />
                        </div>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               placeholder="admin@domain.com"
                               class="w-full neo-input pl-9 pr-3.5 py-2.5 rounded-lg text-sm font-mono">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-mono font-bold text-zinc-300 mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
                            <x-lucide-key class="w-4 h-4" />
                        </div>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="••••••••"
                               class="w-full neo-input pl-9 pr-3.5 py-2.5 rounded-lg text-sm font-mono">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs font-mono">
                    <label class="flex items-center gap-2 cursor-pointer text-zinc-300">
                        <input type="checkbox" name="remember" class="rounded border-zinc-700 bg-zinc-900 text-purple-500 focus:ring-0">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full neo-btn py-3 px-6 rounded-xl bg-purple-500 hover:bg-purple-400 text-black text-sm font-bold font-mono shadow-[3px_3px_0px_0px_#fff]">
                    <x-lucide-log-in class="w-4 h-4 mr-2" />
                    <span>Masuk ke Panel Pengelola</span>
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-800 text-center">
                <a href="{{ route('home') }}" class="text-xs font-mono text-zinc-400 hover:text-amber-400 transition-colors flex items-center justify-center gap-1.5">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" />
                    <span>Kembali ke Halaman Depan</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
