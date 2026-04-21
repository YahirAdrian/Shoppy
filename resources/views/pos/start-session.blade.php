<x-layouts.pos title="Iniciar turno - Shoppy Sales">
    <div class="flex min-h-[70vh] items-center justify-center">
        <div class="w-full max-w-sm">

            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-primary-100">
                    <svg class="h-7 w-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-stone-800">Iniciar turno</h1>
                <p class="mt-1 text-sm text-stone-500">
                    Ingrese el efectivo inicial en caja para comenzar su turno
                </p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm"
                 x-data="posStartSession({
                     storeUrl: '{{ route('pos.api.sessions.store') }}',
                     saleUrl:  '{{ route('pos.sale') }}'
                 })">

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-stone-700">
                        Efectivo en caja al iniciar
                    </label>
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-medium text-stone-500">{{ $currency }}</span>
                        <input type="number" step="0.01" min="0" x-model="amount"
                               @keydown.enter="submit()"
                               placeholder="0.00"
                               class="w-full rounded-lg border border-stone-300 px-4 py-3 text-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                               autofocus>
                    </div>
                    <p class="mt-1 text-xs text-stone-400">
                        Si la caja está vacía, deje el campo en 0.
                    </p>
                </div>

                <p x-show="error" x-text="error"
                   class="mb-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"></p>

                <button type="button" @click="submit()" :disabled="loading"
                        class="w-full rounded-lg bg-primary-600 py-3 text-sm font-semibold text-white
                               hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50">
                    <span x-show="!loading">Iniciar turno</span>
                    <span x-show="loading">Iniciando…</span>
                </button>
            </div>

            <p class="mt-4 text-center text-xs text-stone-400">
                Sesión iniciada como <strong>{{ auth()->user()->name }}</strong>
            </p>

        </div>
    </div>
</x-layouts.pos>
