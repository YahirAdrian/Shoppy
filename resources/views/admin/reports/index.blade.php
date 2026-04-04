<x-layouts.admin title="Reportes — Shoppy Adminer">

    {{-- Page header (hidden on print) --}}
    <div class="print:hidden">
        <h1 class="text-2xl font-bold text-stone-800 uppercase">Reportes</h1>
        <p class="mt-1 text-stone-500">Genera y visualiza reportes de ventas</p>
    </div>

    <div x-data="{
        period: '{{ request('period', 'today') }}',
        categoryId: '{{ request('category_id', '') }}',
        products: @js(\App\Models\Product::select('id', 'name', 'category_id')->orderBy('name')->get()),
        selectedProducts: @js(request('product_ids', \App\Models\Product::pluck('id'))),
        get filteredProducts() {
            if (!this.categoryId) return this.products;
            return this.products.filter(p => p.category_id == this.categoryId);
        },
        toggleProduct(id) {
            const idx = this.selectedProducts.indexOf(id);
            if (idx > -1) {
                this.selectedProducts.splice(idx, 1);
            } else {
                this.selectedProducts.push(id);
            }
        },
        selectAll() {
            this.selectedProducts = this.filteredProducts.map(p => p.id);
        },
        clearAll() {
            this.selectedProducts = [];
        }
    }" class="mt-6">

        {{-- Filter panel (hidden on print) --}}
        <form method="GET" action="{{ route('admin.reports.index') }}" class="print:hidden rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <h3 class="font-semibold text-stone-800">Filtros del reporte</h3>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Periodo --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Periodo</label>
                    <select name="period" x-model="period"
                            class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="today">Hoy</option>
                        <option value="week">Esta semana</option>
                        <option value="month">Este mes</option>
                        <option value="year">Este año</option>
                        <option value="custom">Personalizado</option>
                    </select>
                </div>

                {{-- Custom dates --}}
                <template x-if="period === 'custom'">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-stone-700">Desde</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                   class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-stone-700">Hasta</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                   class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        </div>
                    </div>
                </template>

                {{-- Categoría --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Categoría</label>
                    <select name="category_id" x-model="categoryId"
                            @change="$nextTick(() => selectedProducts = filteredProducts.map(p => p.id))"
                            class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="">Todas</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Productos --}}
                <div class="sm:col-span-2 lg:col-span-2">
                    <label class="block text-sm font-medium text-stone-700">Productos</label>
                    <div class="mt-1 max-h-36 overflow-y-auto rounded-lg border border-stone-300 p-2">
                        <div class="mb-2 flex gap-2 border-b border-stone-200 pb-2">
                            <button type="button" @click="selectAll()" class="text-xs font-medium text-primary-600 hover:text-primary-800">Todos</button>
                            <button type="button" @click="clearAll()" class="text-xs font-medium text-stone-500 hover:text-stone-700">Ninguno</button>
                            <span class="ml-auto text-xs text-stone-400" x-text="selectedProducts.length ? selectedProducts.length + ' seleccionados' : 'Todos (sin filtro)'"></span>
                        </div>
                        <template x-for="product in filteredProducts" :key="product.id">
                            <label class="flex items-center gap-2 rounded px-1 py-0.5 hover:bg-stone-50 cursor-pointer">
                                <input type="checkbox"
                                       :value="product.id"
                                       name="product_ids[]"
                                       :checked="selectedProducts.includes(product.id)"
                                       @change="toggleProduct(product.id)"
                                       class="rounded border-stone-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-stone-700" x-text="product.name"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit"
                        class="rounded-lg bg-primary-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-primary-700 transition">
                    Generar reporte
                </button>
            </div>
        </form>

        {{-- Report preview --}}
        @if($report)
            <div class="mt-6">
                {{-- Report header --}}
                <div class="flex items-center justify-between print:block">
                    <div>
                        {{-- Print-only business header --}}
                        <div class="hidden print:block print:mb-4">
                            <h1 class="text-xl font-bold">Reporte de Ventas</h1>
                        </div>
                        <h2 class="text-lg font-bold text-stone-800 print:text-base">
                            Periodo: {{ $filters['period_label'] }}
                        </h2>
                        <p class="text-sm text-stone-500 print:text-xs">
                            {{ $report['sales_count'] }} {{ $report['sales_count'] === 1 ? 'venta' : 'ventas' }} encontradas
                            @if($filters['category_id'])
                                · Categoría: {{ $categories->firstWhere('id', $filters['category_id'])?->name }}
                            @endif
                        </p>
                    </div>
                    <button onclick="window.print()"
                            class="print:hidden rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50 transition flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/></svg>
                        Imprimir
                    </button>
                </div>

                {{-- Report table --}}
                <div class="mt-4 overflow-x-auto rounded-xl border border-stone-200 bg-white shadow-sm print:shadow-none print:border print:rounded-none">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase text-stone-500 print:bg-stone-100">
                            <tr>
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3">Categoría</th>
                                <th class="px-4 py-3 text-right">Cantidad</th>
                                <th class="px-4 py-3 text-right">Ingresos</th>
                                <th class="px-4 py-3 text-right">Descuentos</th>
                                <th class="px-4 py-3 text-right">Neto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($report['rows'] as $row)
                                <tr class="hover:bg-stone-50 print:hover:bg-transparent">
                                    <td class="px-4 py-3 font-medium text-stone-800">{{ $row->product_name }}</td>
                                    <td class="px-4 py-3 text-stone-600">{{ $row->category_name }}</td>
                                    <td class="px-4 py-3 text-right text-stone-600">{{ number_format($row->total_quantity, 0) }}</td>
                                    <td class="px-4 py-3 text-right text-stone-600">{{ $currency }}{{ number_format($row->total_revenue, 2) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if($row->total_discount > 0)
                                            <span class="text-red-500">-{{ $currency }}{{ number_format($row->total_discount, 2) }}</span>
                                        @else
                                            <span class="text-stone-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-stone-800">{{ $currency }}{{ number_format($row->total_net, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-stone-400">
                                        No se encontraron ventas para los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($report['rows']->count() > 0)
                            <tfoot class="border-t-2 border-stone-300 bg-stone-50 font-semibold print:bg-stone-100">
                                <tr>
                                    <td class="px-4 py-3 text-stone-800" colspan="2">Totales</td>
                                    <td class="px-4 py-3 text-right text-stone-800">{{ number_format($report['totals']['quantity'], 0) }}</td>
                                    <td class="px-4 py-3 text-right text-stone-800">{{ $currency }}{{ number_format($report['totals']['revenue'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-red-600">
                                        @if($report['totals']['discount'] > 0)
                                            -{{ $currency }}{{ number_format($report['totals']['discount'], 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-stone-900 text-base">{{ $currency }}{{ number_format($report['totals']['net'], 2) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Print footer --}}
                <div class="hidden print:block print:mt-6 print:text-center print:text-xs print:text-stone-400">
                    Reporte generado el {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        @endif

    </div>

</x-layouts.admin>
