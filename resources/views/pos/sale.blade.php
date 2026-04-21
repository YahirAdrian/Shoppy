<x-layouts.pos title="Venta - Shoppy Sales">
    <div x-data="posSale({
            searchUrl: '{{ route('pos.api.products') }}',
            storeUrl: '{{ route('pos.api.sales.store') }}',
            currency: @js($currency),
            business: @js($business)
         })" x-init="init()" x-cloak>

        {{-- Added-from-search toast --}}
        <div x-show="addedToast"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-4 flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Producto agregado al carrito
        </div>

        {{-- Page header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold uppercase text-stone-800">Venta</h1>
                <p class="mt-1 text-stone-500">Agregue productos y registre la venta</p>
            </div>
            <div class="flex gap-2">
                <button type="button" @click="resetSale()"
                        class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50"
                        :disabled="cart.length === 0">
                    Reiniciar venta
                </button>
                <button type="button" @click="openPayment()"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="cart.length === 0">
                    Confirmar venta
                </button>
            </div>
        </div>

        {{-- Search bar --}}
        <div class="mb-4 rounded-xl bg-white p-4 shadow-sm">
            <label for="pos-search" class="mb-1 block text-sm font-medium text-stone-700">Buscar producto</label>
            <div class="relative">
                <input id="pos-search" type="text" x-model="query" @input="scheduleSearch()"
                       @keydown.enter.prevent="searchNow()"
                       placeholder="Código de barras o nombre del producto"
                       class="w-full rounded-lg border border-stone-300 px-4 py-3 text-base focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                <div x-show="searching" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-stone-500">Buscando…</div>
            </div>
            <p x-show="searchMessage" x-text="searchMessage" class="mt-2 text-sm text-red-600"></p>
        </div>

        {{-- Product results --}}
        <div x-show="searchResults.length > 1" class="mb-4">
            <h2 class="mb-2 text-sm font-semibold uppercase text-stone-600">Resultados</h2>
            <div class="flex gap-3 overflow-x-auto pb-2">
                <template x-for="product in searchResults" :key="product.id">
                    <div class="flex w-48 shrink-0 flex-col rounded-lg border border-stone-200 bg-white p-3 shadow-sm">
                        <div class="mb-2 flex h-24 w-full items-center justify-center overflow-hidden rounded bg-stone-100">
                            <template x-if="product.image">
                                <img :src="product.image" :alt="product.name" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!product.image">
                                <span class="text-2xl text-stone-400">📦</span>
                            </template>
                        </div>
                        <p class="truncate text-sm font-semibold text-stone-800" x-text="product.name"></p>
                        <p class="text-xs text-stone-500" x-text="product.category"></p>
                        <p class="mt-1 text-base font-bold text-primary-700">
                            <span>{{ $currency }}</span><span x-text="product.selling_price.toFixed(2)"></span>
                        </p>
                        <p class="text-xs text-stone-500">Stock: <span x-text="product.stock"></span></p>
                        <button type="button" @click="addToCart(product)"
                                class="mt-2 rounded bg-primary-600 px-2 py-1 text-xs font-medium text-white hover:bg-primary-700">
                            Agregar
                        </button>
                    </div>
                </template>
            </div>
        </div>

        @include('pos.partials.cart-table', ['currency' => $currency])

        @include('pos.partials.payment-modal', ['currency' => $currency])
        @include('pos.partials.stock-warning-modal', ['currency' => $currency])

    </div>
</x-layouts.pos>
