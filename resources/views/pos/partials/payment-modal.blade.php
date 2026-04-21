{{-- Payment Modal --}}
<div x-show="showPaymentModal" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div class="w-full max-w-lg rounded-xl bg-white shadow-xl"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.outside="showPaymentModal = false">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-stone-800">Registrar venta</h3>
            <button type="button" @click="showPaymentModal = false" class="text-stone-400 hover:text-stone-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="space-y-4 px-6 py-5">

            {{-- Total display --}}
            <div class="rounded-lg bg-primary-50 px-4 py-3 text-center">
                <p class="text-xs uppercase text-primary-700">Total a cobrar</p>
                <p class="text-3xl font-bold text-primary-700">
                    {{ $currency }}<span x-text="total().toFixed(2)"></span>
                </p>
            </div>

            {{-- Payment method --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">Método de pago</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex cursor-pointer items-center justify-center rounded-lg border-2 py-2 text-sm font-medium"
                           :class="payment.method === 'cash' ? 'border-primary-600 bg-primary-50 text-primary-700' : 'border-stone-200 text-stone-600'">
                        <input type="radio" value="cash" x-model="payment.method" class="sr-only">
                        Efectivo
                    </label>
                    <label class="flex cursor-not-allowed items-center justify-center rounded-lg border-2 border-stone-200 bg-stone-50 py-2 text-sm text-stone-400">
                        <input type="radio" value="card" disabled class="sr-only">
                        Tarjeta
                    </label>
                    <label class="flex cursor-not-allowed items-center justify-center rounded-lg border-2 border-stone-200 bg-stone-50 py-2 text-sm text-stone-400">
                        <input type="radio" value="other" disabled class="sr-only">
                        Otro
                    </label>
                </div>
            </div>

            {{-- Tendered amount --}}
            <div>
                <label for="tendered" class="mb-1 block text-sm font-medium text-stone-700">Monto recibido</label>
                <div class="flex items-center gap-2">
                    <span class="text-lg text-stone-500">{{ $currency }}</span>
                    <input id="tendered" type="number" step="0.01" min="0" x-model="payment.tendered"
                           class="w-full rounded-lg border border-stone-300 px-4 py-2 text-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>
            </div>

            {{-- Change --}}
            <div class="flex items-center justify-between rounded-lg bg-stone-100 px-4 py-3">
                <span class="text-sm font-medium text-stone-700">Cambio</span>
                <span class="text-xl font-bold text-stone-800">
                    {{ $currency }}<span x-text="changeAmount().toFixed(2)"></span>
                </span>
            </div>

            {{-- Optional fields toggle --}}
            <button type="button" @click="showOptional = !showOptional"
                    class="text-sm font-medium text-primary-600 hover:text-primary-700">
                <span x-show="!showOptional">+ Mostrar campos opcionales</span>
                <span x-show="showOptional">− Ocultar campos opcionales</span>
            </button>

            {{-- Submit error --}}
            <p x-show="submitError" x-text="submitError"
               class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"></p>

            {{-- Optional: customer + note --}}
            <div x-show="showOptional" class="space-y-3">
                <div>
                    <label for="customer" class="mb-1 block text-sm font-medium text-stone-700">Nombre del cliente</label>
                    <input id="customer" type="text" x-model="payment.customer_name"
                           class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>
                <div>
                    <label for="note" class="mb-1 block text-sm font-medium text-stone-700">Nota</label>
                    <textarea id="note" rows="2" x-model="payment.note"
                              class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"></textarea>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2 border-t border-stone-200 px-6 py-4">
            <button type="button" @click="showPaymentModal = false"
                    class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">
                Cancelar
            </button>
            <button type="button" @click="submitSale()"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!canSubmit()">
                <span x-show="!submitting">Registrar venta</span>
                <span x-show="submitting">Registrando…</span>
            </button>
        </div>
    </div>
</div>
