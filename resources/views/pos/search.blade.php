<x-layouts.pos title="Buscar productos - Shoppy Sales">
    <div x-data="posSearch({
            searchUrl: '{{ route('pos.api.products') }}',
            saleUrl: '{{ route('pos.sale') }}',
            currency: @js($currency)
         })" x-init="init()" x-cloak>

        {{-- Page header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold uppercase text-stone-800">Buscar productos</h1>
            <p class="mt-1 text-stone-500">Seleccione un producto para agregarlo a la venta</p>
        </div>

        {{-- Search bar --}}
        <div class="mb-4 rounded-xl bg-white p-4 shadow-sm">
            <label for="search-input" class="mb-1 block text-sm font-medium text-stone-700">Buscar</label>
            <input id="search-input" type="text" x-model="query" @input="scheduleSearch()"
                   placeholder="Código de barras o nombre del producto"
                   class="w-full rounded-lg border border-stone-300 px-4 py-3 text-base focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        </div>

        {{-- Category pills --}}
        <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
            <button type="button"
                    @click="selectCategory(null)"
                    :class="activeCategoryId === null
                        ? 'bg-primary-600 text-white'
                        : 'bg-white text-stone-700 border border-stone-300 hover:bg-stone-50'"
                    class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium transition-colors">
                Todos
            </button>
            @foreach ($categories as $category)
                <button type="button"
                        @click="selectCategory({{ $category->id }})"
                        :class="activeCategoryId === {{ $category->id }}
                            ? 'bg-primary-600 text-white'
                            : 'bg-white text-stone-700 border border-stone-300 hover:bg-stone-50'"
                        class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium transition-colors">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- Results count + loading --}}
        <div class="mb-3 flex items-center gap-3 text-sm text-stone-500">
            <span x-show="!loading" x-text="totalProducts + ' producto(s) encontrado(s)'"></span>
            <span x-show="loading">Cargando…</span>
        </div>

        {{-- Product grid --}}
        <div x-show="!loading && products.length === 0"
             class="rounded-xl bg-white py-16 text-center shadow-sm text-stone-400">
            No se encontraron productos
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            <template x-for="product in products" :key="product.id">
                <div class="flex flex-col overflow-hidden rounded-xl bg-white shadow-sm">

                    {{-- Product image --}}
                    <div class="relative flex h-32 w-full items-center justify-center bg-stone-100">
                        <template x-if="product.image">
                            <img :src="product.image" :alt="product.name"
                                 class="h-full w-full object-cover">
                        </template>
                        <template x-if="!product.image">
                            <span class="text-4xl text-stone-300">📦</span>
                        </template>

                        {{-- Stock badge --}}
                        <template x-if="isOutOfStock(product)">
                            <span class="absolute right-2 top-2 rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                Sin stock
                            </span>
                        </template>
                        <template x-if="!isOutOfStock(product) && isLowStock(product)">
                            <span class="absolute right-2 top-2 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">
                                Stock bajo
                            </span>
                        </template>
                    </div>

                    {{-- Product info --}}
                    <div class="flex flex-1 flex-col p-3">
                        <p class="line-clamp-2 text-sm font-semibold leading-tight text-stone-800" x-text="product.name"></p>
                        <p class="mt-1 text-xs text-stone-500" x-text="product.category || '—'"></p>

                        <div class="mt-2 flex items-end justify-between">
                            <div>
                                <p class="text-base font-bold text-primary-700">
                                    {{ $currency }}<span x-text="product.selling_price.toFixed(2)"></span>
                                </p>
                                <p class="text-xs text-stone-400">
                                    Stock: <span x-text="product.stock"></span>
                                </p>
                            </div>
                        </div>

                        <button type="button"
                                @click="addToSale(product)"
                                :disabled="isOutOfStock(product)"
                                :class="isOutOfStock(product)
                                    ? 'cursor-not-allowed bg-stone-200 text-stone-400'
                                    : 'bg-primary-600 text-white hover:bg-primary-700'"
                                class="mt-3 w-full rounded-lg py-2 text-xs font-semibold transition-colors">
                            <span x-show="!isOutOfStock(product)">Agregar producto</span>
                            <span x-show="isOutOfStock(product)">Sin stock</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Pagination --}}
        <div x-show="lastPage > 1" class="mt-6 flex items-center justify-center gap-4">
            <button type="button" @click="prevPage()" :disabled="currentPage <= 1"
                    class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700
                           hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-50">
                ← Anterior
            </button>
            <span class="text-sm text-stone-600">
                Página <span x-text="currentPage"></span> de <span x-text="lastPage"></span>
            </span>
            <button type="button" @click="nextPage()" :disabled="currentPage >= lastPage"
                    class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700
                           hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-50">
                Siguiente →
            </button>
        </div>

    </div>
</x-layouts.pos>
