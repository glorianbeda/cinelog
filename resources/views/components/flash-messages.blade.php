@if (session('success'))
    <div class="mb-6 flex items-center justify-between p-4 bg-emerald-500/15 border-2 border-emerald-500 rounded-lg text-emerald-300 font-bold font-mono text-sm shadow-[4px_4px_0px_0px_#10B981]">
        <div class="flex items-center gap-2.5">
            <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-400" />
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">
            <x-lucide-x class="w-4 h-4" />
        </button>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 flex items-center justify-between p-4 bg-rose-500/15 border-2 border-rose-500 rounded-lg text-rose-300 font-bold font-mono text-sm shadow-[4px_4px_0px_0px_#F43F5E]">
        <div class="flex items-center gap-2.5">
            <x-lucide-alert-triangle class="w-5 h-5 text-rose-400" />
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white">
            <x-lucide-x class="w-4 h-4" />
        </button>
    </div>
@endif

@if (session('info'))
    <div class="mb-6 flex items-center justify-between p-4 bg-cyan-500/15 border-2 border-cyan-500 rounded-lg text-cyan-300 font-bold font-mono text-sm shadow-[4px_4px_0px_0px_#06B6D4]">
        <div class="flex items-center gap-2.5">
            <x-lucide-info class="w-5 h-5 text-cyan-400" />
            <span>{{ session('info') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-cyan-400 hover:text-white">
            <x-lucide-x class="w-4 h-4" />
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 bg-rose-500/15 border-2 border-rose-500 rounded-lg text-rose-300 font-mono text-sm shadow-[4px_4px_0px_0px_#F43F5E] space-y-1">
        <div class="font-bold flex items-center gap-2 text-rose-400">
            <x-lucide-alert-circle class="w-5 h-5" />
            <span>Mohon perbaiki kesalahan berikut:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-200 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
