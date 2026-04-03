{{-- Stock adjustment modal --}}
<div x-show="showStockModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div @click.outside="showStockModal = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-bold text-stone-800">Ajustar stock</h3>
        <p class="mt-1 text-sm text-stone-500">
            Producto: <span class="font-medium text-stone-700" x-text="stockProduct.name"></span>
        </p>

        <div class="mt-3 rounded-lg bg-stone-50 px-4 py-3">
            <span class="text-sm text-stone-500">Stock actual:</span>
            <span class="ml-2 text-lg font-bold text-stone-800" x-text="stockProduct.stock"></span>
        </div>

        <form :action="'{{ url('admin/inventario/productos') }}/' + stockProduct.id + '/ajuste'"
              method="POST" class="mt-4">
            @csrf

            <div class="space-y-4">
                {{-- Cantidad --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Cantidad (positivo para agregar, negativo para restar) <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" x-model="stockForm.quantity" step="1" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                           placeholder="Ej: 10 o -5">
                </div>

                {{-- Nota --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Nota / Razón <span class="text-red-500">*</span></label>
                    <textarea name="note" x-model="stockForm.note" rows="2" required
                              class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                              placeholder="Ej: Reabastecimiento de proveedor"></textarea>
                </div>
            </div>

            {{-- Nuevo stock estimado --}}
            <div class="mt-3 rounded-lg bg-primary-50 px-4 py-3" x-show="stockForm.quantity">
                <span class="text-sm text-primary-700">Nuevo stock estimado:</span>
                <span class="ml-2 text-lg font-bold text-primary-800" x-text="Number(stockProduct.stock) + Number(stockForm.quantity || 0)"></span>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="showStockModal = false"
                        class="rounded-lg border border-stone-300 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                    Ajustar stock
                </button>
            </div>
        </form>
    </div>
</div>
