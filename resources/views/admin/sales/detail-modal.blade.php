{{-- Sale detail modal --}}
<div x-show="showDetailModal" x-cloak
     class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 py-8"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div @click.outside="showDetailModal = false" class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">

        {{-- Loading state --}}
        <template x-if="loading">
            <div class="flex items-center justify-center py-12">
                <svg class="h-8 w-8 animate-spin text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </template>

        {{-- Sale detail content --}}
        <template x-if="!loading && saleDetail">
            <div>
                {{-- Header --}}
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-stone-800">
                            Venta #<span x-text="saleDetail.id"></span>
                        </h3>
                        <p class="mt-1 text-sm text-stone-500" x-text="new Date(saleDetail.created_at).toLocaleString('es-MX', { dateStyle: 'long', timeStyle: 'short' })"></p>
                    </div>
                    <button @click="showDetailModal = false" class="rounded-lg p-1 text-stone-400 hover:text-stone-700 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Sale info --}}
                <div class="mt-4 grid grid-cols-2 gap-3 rounded-lg bg-stone-50 p-4 text-sm">
                    <div>
                        <span class="text-stone-500">Vendedor:</span>
                        <span class="ml-1 font-medium text-stone-800" x-text="saleDetail.user.name"></span>
                    </div>
                    <div>
                        <span class="text-stone-500">Cliente:</span>
                        <span class="ml-1 font-medium text-stone-800" x-text="saleDetail.customer_name || '—'"></span>
                    </div>
                    <div>
                        <span class="text-stone-500">Método de pago:</span>
                        <template x-if="saleDetail.payment_method === 'cash'">
                            <span class="ml-1 inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Efectivo</span>
                        </template>
                        <template x-if="saleDetail.payment_method === 'card'">
                            <span class="ml-1 inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">Tarjeta</span>
                        </template>
                    </div>
                    <template x-if="saleDetail.note">
                        <div>
                            <span class="text-stone-500">Nota:</span>
                            <span class="ml-1 text-stone-800" x-text="saleDetail.note"></span>
                        </div>
                    </template>
                </div>

                {{-- Items table --}}
                <div class="mt-4 overflow-x-auto rounded-lg border border-stone-200">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase text-stone-500">
                            <tr>
                                <th class="px-4 py-2">Producto</th>
                                <th class="px-4 py-2 text-center">Cant.</th>
                                <th class="px-4 py-2 text-right">P. Unitario</th>
                                <th class="px-4 py-2 text-right">Descuento</th>
                                <th class="px-4 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <template x-for="item in saleDetail.items" :key="item.id">
                                <tr>
                                    <td class="px-4 py-2 text-stone-800" x-text="item.product_name"></td>
                                    <td class="px-4 py-2 text-center text-stone-600" x-text="parseFloat(item.quantity)"></td>
                                    <td class="px-4 py-2 text-right text-stone-600">
                                        <span>{{ $currency }}</span><span x-text="parseFloat(item.unit_price).toFixed(2)"></span>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <template x-if="parseFloat(item.discount_amount) > 0">
                                            <span class="text-red-500">-{{ $currency }}<span x-text="parseFloat(item.discount_amount).toFixed(2)"></span></span>
                                        </template>
                                        <template x-if="parseFloat(item.discount_amount) === 0">
                                            <span class="text-stone-400">—</span>
                                        </template>
                                    </td>
                                    <td class="px-4 py-2 text-right font-medium text-stone-800">
                                        <span>{{ $currency }}</span><span x-text="parseFloat(item.subtotal).toFixed(2)"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="mt-4 space-y-1 rounded-lg bg-stone-50 p-4 text-sm">
                    <div class="flex justify-between text-stone-600">
                        <span>Subtotal</span>
                        <span>{{ $currency }}<span x-text="parseFloat(saleDetail.subtotal).toFixed(2)"></span></span>
                    </div>
                    <template x-if="parseFloat(saleDetail.discount_amount) > 0">
                        <div class="flex justify-between text-red-500">
                            <span>Descuento</span>
                            <span>-{{ $currency }}<span x-text="parseFloat(saleDetail.discount_amount).toFixed(2)"></span></span>
                        </div>
                    </template>
                    <div class="flex justify-between border-t border-stone-200 pt-2 text-base font-bold text-stone-800">
                        <span>Total</span>
                        <span>{{ $currency }}<span x-text="(parseFloat(saleDetail.subtotal) - parseFloat(saleDetail.discount_amount)).toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-stone-500">
                        <span>Monto recibido</span>
                        <span>{{ $currency }}<span x-text="parseFloat(saleDetail.amount_tendered).toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-stone-500">
                        <span>Cambio</span>
                        <span>{{ $currency }}<span x-text="parseFloat(saleDetail.change_given).toFixed(2)"></span></span>
                    </div>
                </div>
            </div>
        </template>

    </div>
</div>
