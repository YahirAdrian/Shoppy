@forelse($categories as $category)
    @php $products = $productsByCategory->get($category->id, collect()); @endphp
    @if($products->isNotEmpty())
        <div class="mt-6">
            <h3 class="flex items-center gap-2 text-lg font-bold text-stone-700">
                {{ $category->name }}
                <span class="rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700">{{ $products->count() }}</span>
            </h3>

            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $product)
                    <div class="group relative rounded-xl border border-stone-200 bg-white shadow-sm hover:shadow-md transition">
                        {{-- Image header --}}
                        <div class="flex h-36 items-center justify-center rounded-t-xl bg-stone-100">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full rounded-t-xl object-cover">
                            @else
                                <svg class="h-12 w-12 text-stone-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Low stock badge --}}
                        @if($product->isLowStock())
                            <span class="absolute top-2 left-2 rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Stock bajo</span>
                        @endif

                        {{-- Kebab menu --}}
                        <div x-data="{ open: false }" class="absolute top-2 right-2">
                            <button @click="open = !open" class="rounded-full bg-white/80 p-1 shadow hover:bg-white transition">
                                <svg class="h-5 w-5 text-stone-600" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
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
                                    is_active: {{ $product->is_active ? 'true' : 'false' }}
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

                        {{-- Product info --}}
                        <div class="p-4">
                            <h4 class="font-semibold text-stone-800 truncate">{{ $product->name }}</h4>
                            <dl class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                                <dt class="text-stone-400">SKU</dt>
                                <dd class="text-stone-700 font-medium">{{ $product->sku }}</dd>
                                <dt class="text-stone-400">Precio</dt>
                                <dd class="text-stone-700 font-medium">{{ $currency }}{{ number_format($product->selling_price, 2) }}</dd>
                                <dt class="text-stone-400">Costo</dt>
                                <dd class="text-stone-700 font-medium">{{ $currency }}{{ number_format($product->cost_price, 2) }}</dd>
                                <dt class="text-stone-400">Stock</dt>
                                <dd class="font-medium {{ $product->isLowStock() ? 'text-red-600' : 'text-stone-700' }}">{{ number_format($product->stock, 0) }} {{ $product->unit }}</dd>
                            </dl>
                            @if(!$product->is_active)
                                <span class="mt-2 inline-block rounded-full bg-stone-200 px-2 py-0.5 text-xs text-stone-500">Inactivo</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@empty
    <div class="mt-8 text-center text-stone-400">
        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
        <p class="mt-2 text-sm">No hay productos registrados.</p>
    </div>
@endforelse
