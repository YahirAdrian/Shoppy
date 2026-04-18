{{-- Cart table --}}
<div class="overflow-hidden rounded-xl bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-600">
                <tr>
                    <th class="px-4 py-3 text-left">Producto</th>
                    <th class="px-4 py-3 text-left">Categoría</th>
                    <th class="px-4 py-3 text-left">Código</th>
                    <th class="px-4 py-3 text-right">Precio</th>
                    <th class="px-4 py-3 text-center">Cantidad</th>
                    <th class="px-4 py-3 text-right">Descuento</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200">
                <template x-if="cart.length === 0">
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-stone-400">
                            No hay productos en la venta
                        </td>
                    </tr>
                </template>
                <template x-for="(item, index) in cart" :key="item.id">
                    <tr :class="item.quantity > item.stock ? 'bg-red-50' : ''">
                        <td class="px-4 py-3 font-medium text-stone-800" x-text="item.name"></td>
                        <td class="px-4 py-3 text-stone-600" x-text="item.category"></td>
                        <td class="px-4 py-3 font-mono text-xs text-stone-500" x-text="item.barcode || '—'"></td>
                        <td class="px-4 py-3 text-right text-stone-700">
                            {{ $currency }}<span x-text="item.unit_price.toFixed(2)"></span>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" step="1" x-model.number="item.quantity"
                                   @input="persist()"
                                   class="w-20 rounded border border-stone-300 px-2 py-1 text-center text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            <p x-show="item.quantity > item.stock" class="mt-1 text-xs text-red-600">
                                Stock: <span x-text="item.stock"></span>
                            </p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <span class="text-stone-500">{{ $currency }}</span>
                                <input type="number" step="1" min="0" x-model.number="item.discount"
                                       @input="persist()"
                                       class="w-20 rounded border border-stone-300 px-2 py-1 text-right text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-stone-800">
                            {{ $currency }}<span x-text="lineSubtotal(item).toFixed(2)"></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" @click="removeItem(index)"
                                    class="text-red-500 hover:text-red-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
            <tfoot x-show="cart.length > 0" class="bg-stone-50 font-semibold text-stone-800">
                <tr>
                    <td colspan="6" class="px-4 py-3 text-right">Subtotal</td>
                    <td class="px-4 py-3 text-right">
                        {{ $currency }}<span x-text="subtotal().toFixed(2)"></span>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6" class="px-4 py-3 text-right">Descuento</td>
                    <td class="px-4 py-3 text-right text-red-600">
                        -{{ $currency }}<span x-text="totalDiscount().toFixed(2)"></span>
                    </td>
                    <td></td>
                </tr>
                <tr class="text-lg">
                    <td colspan="6" class="px-4 py-3 text-right">Total</td>
                    <td class="px-4 py-3 text-right text-primary-700">
                        {{ $currency }}<span x-text="total().toFixed(2)"></span>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
