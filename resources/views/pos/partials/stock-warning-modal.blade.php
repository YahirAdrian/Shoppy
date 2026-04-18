{{-- Stock Warning Modal --}}
<div x-show="showStockWarning" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div class="w-full max-w-md rounded-xl bg-white shadow-xl"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.outside="showStockWarning = false">

        {{-- Header --}}
        <div class="flex items-center gap-3 border-b border-red-200 bg-red-50 px-6 py-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-red-800">Stock insuficiente</h3>
        </div>

        {{-- Body --}}
        <div class="space-y-3 px-6 py-5">
            <p class="text-sm text-stone-700">
                Los siguientes productos no tienen stock suficiente. ¿Desea continuar de todas formas?
            </p>
            <ul class="divide-y divide-stone-200 rounded-lg border border-stone-200">
                <template x-for="item in stockIssues" :key="item.id">
                    <li class="flex items-center justify-between px-3 py-2 text-sm">
                        <span class="font-medium text-stone-800" x-text="item.name"></span>
                        <span class="text-xs text-stone-500">
                            Pedido: <span class="font-semibold text-red-600" x-text="item.quantity"></span>
                            · Disponible: <span x-text="item.stock"></span>
                        </span>
                    </li>
                </template>
            </ul>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2 border-t border-stone-200 px-6 py-4">
            <button type="button" @click="showStockWarning = false"
                    class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">
                Cancelar
            </button>
            <button type="button" @click="proceedDespiteStock()"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700">
                Continuar de todas formas
            </button>
        </div>
    </div>
</div>
