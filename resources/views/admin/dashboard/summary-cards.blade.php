<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

    {{-- Ventas --}}
    <div class="rounded-xl bg-gradient-to-br from-primary-600 to-primary-800 p-5 text-white shadow"
         x-data="{
             period: 'daily',
             stats: @js($salesStats),
             currency: @js($currency),
             recentSales: @js($recentSales),
             fmt(n) {
                 return this.currency + ' ' + Number(n).toLocaleString('es', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
             }
         }">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-wide opacity-90">Ventas</h2>
            <select x-model="period"
                    class="rounded-md bg-white/20 px-2 py-1 text-xs text-white backdrop-blur focus:outline-none">
                <option value="daily">Hoy</option>
                <option value="weekly">Semanal</option>
                <option value="monthly">Mensual</option>
            </select>
        </div>
        <p class="mt-3 text-3xl font-bold" x-text="stats[period].count"></p>
        <p class="mt-1 text-sm opacity-80" x-text="fmt(stats[period].revenue) + ' en ingresos'"></p>
        <hr class="my-3 border-white/20">
        <p class="mb-2 text-xs font-semibold uppercase opacity-70">Ventas recientes</p>
        <template x-if="recentSales.length === 0">
            <p class="text-sm opacity-70">Sin ventas registradas.</p>
        </template>
        <ul class="space-y-1 text-sm">
            <template x-for="sale in recentSales" :key="sale.id">
                <li class="flex justify-between">
                    <span x-text="'Venta #' + sale.id"></span>
                    <span x-text="fmt(sale.total)"></span>
                </li>
            </template>
        </ul>
    </div>

    {{-- Productos --}}
    <div class="rounded-xl bg-gradient-to-br from-green-500 to-green-700 p-5 text-white shadow">
        <h2 class="text-sm font-semibold uppercase tracking-wide opacity-90">Productos</h2>
        <div class="mt-3 grid grid-cols-2 gap-4">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase opacity-70">Más vendidos</p>
                <div class="space-y-1">
                    @forelse ($topProducts as $product)
                        <div class="flex items-center gap-2 rounded-lg bg-white/20 px-2.5 py-1.5 text-sm">
                            <span class="shrink-0 font-bold opacity-70">{{ $loop->iteration }}.</span>
                            <span class="truncate">{{ $product->product_name }}</span>
                        </div>
                    @empty
                        <p class="text-sm opacity-70">Sin datos de ventas.</p>
                    @endforelse
                </div>
            </div>
            <div>
                <p class="mb-2 text-xs font-semibold uppercase opacity-70">Stock bajo</p>
                <div class="space-y-1">
                    @forelse ($lowStockProducts as $product)
                        <div class="flex items-center justify-between rounded-lg bg-white/20 px-2.5 py-1.5 text-sm">
                            <span class="truncate pr-1">{{ $product->name }}</span>
                            <span class="shrink-0 font-bold">{{ (float) $product->stock }}</span>
                        </div>
                    @empty
                        <p class="text-sm opacity-70">Sin alertas de stock.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Vendedores --}}
    <div class="rounded-xl bg-gradient-to-br from-accent-400 to-accent-600 p-5 text-white shadow"
         x-data="{ confirmSession: null }">
        <h2 class="text-sm font-semibold uppercase tracking-wide opacity-90">Vendedores</h2>
        <hr class="my-3 border-white/20">
        @forelse ($sellers as $seller)
            <div class="flex items-center justify-between py-2 text-sm">
                <div>
                    <p class="font-medium">{{ $seller['name'] }}</p>
                    <p class="text-xs opacity-80">{{ $seller['today_sales'] }} {{ $seller['today_sales'] === 1 ? 'venta hoy' : 'ventas hoy' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($seller['session'])
                        <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs font-semibold">En turno</span>
                        <button type="button"
                                @click="confirmSession = {
                                    id: {{ $seller['session']['id'] }},
                                    name: @js($seller['name']),
                                    canEnd: {{ $seller['session']['can_end'] ? 'true' : 'false' }}
                                }"
                                class="rounded-md bg-white/20 px-2 py-1 text-xs font-semibold hover:bg-white/30 transition">
                            Terminar turno
                        </button>
                    @else
                        <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs opacity-70">Sin turno</span>
                    @endif
                </div>
            </div>
            @if (! $loop->last)
                <hr class="border-white/10">
            @endif
        @empty
            <p class="text-sm opacity-70">Sin vendedores activos.</p>
        @endforelse

        {{-- Confirm dialog --}}
        <div x-show="confirmSession !== null"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             @keydown.escape.window="confirmSession = null">
            <div class="mx-4 w-full max-w-sm rounded-xl bg-white p-6 shadow-xl text-stone-800"
                 @click.stop>
                <h3 class="text-lg font-bold">Terminar turno</h3>
                <p class="mt-2 text-sm text-stone-600">
                    ¿Confirmas terminar el turno de <strong x-text="confirmSession?.name"></strong>?
                </p>
                <template x-if="confirmSession && !confirmSession.canEnd">
                    <p class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                        El vendedor tiene efectivo en caja. Debe retirarlo desde el POS antes de terminar la sesión.
                    </p>
                </template>
                <div class="mt-5 flex justify-end gap-3">
                    <button type="button"
                            @click="confirmSession = null"
                            class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50 transition">
                        Cancelar
                    </button>
                    <form :action="'{{ url('admin/dashboard/end-session') }}/' + confirmSession?.id"
                          method="POST">
                        @csrf
                        <button type="submit"
                                :disabled="!confirmSession?.canEnd"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            Terminar turno
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
