<div class="mt-4 overflow-x-auto rounded-xl border border-stone-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase text-stone-500">
            <tr>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">SKU</th>
                <th class="px-4 py-3">Categoría</th>
                <th class="px-4 py-3">Precio Venta</th>
                <th class="px-4 py-3">Costo</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3">Unidad</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @php $allProducts = $productsByCategory->flatten(); @endphp
            @forelse($allProducts as $product)
                <tr class="hover:bg-stone-50 transition">
                    <td class="px-4 py-3 font-medium text-stone-800">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-stone-600">{{ $product->sku }}</td>
                    <td class="px-4 py-3 text-stone-600">{{ $product->category->name }}</td>
                    <td class="px-4 py-3 text-stone-700 font-medium">{{ $currency }}{{ number_format($product->selling_price, 2) }}</td>
                    <td class="px-4 py-3 text-stone-500">{{ $currency }}{{ number_format($product->cost_price, 2) }}</td>
                    <td class="px-4 py-3 font-medium {{ $product->isLowStock() ? 'text-red-600' : 'text-stone-700' }}">
                        {{ number_format($product->stock, 0) }}
                        @if($product->isLowStock())
                            <span class="ml-1 inline-block h-2 w-2 rounded-full bg-red-500"></span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-stone-600">{{ $product->unit }}</td>
                    <td class="px-4 py-3">
                        @if($product->is_active)
                            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Activo</span>
                        @else
                            <span class="rounded-full bg-stone-200 px-2 py-0.5 text-xs font-medium text-stone-500">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div x-data="{ open: false }" class="relative inline-block">
                            <button @click="open = !open" class="rounded p-1 text-stone-400 hover:text-stone-700 transition">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 mt-1 w-40 rounded-lg bg-white py-1 shadow-lg ring-1 ring-stone-200 z-10">
                                <button @click="open = false; openEditProduct({
                                    id: {{ $product->id }},
                                    name: '{{ addslashes($product->name) }}',
                                    sku: '{{ $product->sku }}',
                                    barcode: '{{ $product->barcode }}',
                                    category_id: '{{ $product->category_id }}',
                                    description: `{{ addslashes($product->description) }}`,
                                    cost_price: '{{ $product->cost_price }}',
                                    selling_price: '{{ $product->selling_price }}',
                                    low_stock_alert: '{{ $product->low_stock_alert }}',
                                    unit: '{{ $product->unit }}',
                                    is_active: {{ $product->is_active ? 'true' : 'false' }},
                                    image: '{{ $product->image }}'
                                })" class="flex w-full items-center gap-2 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                    Editar
                                </button>
                                <button @click="open = false; openStockAdjustment({ id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', stock: {{ $product->stock }} })"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    Ajustar stock
                                </button>
                                <button @click="open = false; openDelete('product', {{ $product->id }}, '{{ addslashes($product->name) }}')"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-stone-400">No hay productos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
