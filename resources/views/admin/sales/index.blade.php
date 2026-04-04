<x-layouts.admin title="Ventas — Shoppy Adminer">

    {{-- Page header --}}
    <h1 class="text-2xl font-bold text-stone-800 uppercase">Ventas</h1>
    <p class="mt-1 text-stone-500">Historial de ventas realizadas</p>

    {{-- Main container --}}
    <div x-data="{
        showDetailModal: false,
        saleDetail: null,
        loading: false,
        async openDetail(saleId) {
            this.loading = true;
            this.showDetailModal = true;
            try {
                const res = await fetch('/admin/ventas/' + saleId);
                const data = await res.json();
                this.saleDetail = data.sale;
            } catch (e) {
                this.saleDetail = null;
            }
            this.loading = false;
        }
    }" class="mt-6">

        {{-- Sales table --}}
        <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Vendedor</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3 text-center">Productos</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                        <th class="px-4 py-3 text-right">Descuento</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Método</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="px-4 py-3 font-medium text-stone-800">{{ $sale->id }}</td>
                            <td class="px-4 py-3 text-stone-600">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-stone-600">{{ $sale->user->name }}</td>
                            <td class="px-4 py-3 text-stone-600">{{ $sale->customer_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-stone-600">{{ $sale->items_count }}</td>
                            <td class="px-4 py-3 text-right text-stone-600">{{ $currency }}{{ number_format($sale->subtotal, 2) }}</td>
                            <td class="px-4 py-3 text-right text-stone-600">
                                @if($sale->discount_amount > 0)
                                    <span class="text-red-500">-{{ $currency }}{{ number_format($sale->discount_amount, 2) }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-stone-800">{{ $currency }}{{ number_format($sale->total, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($sale->payment_method === 'cash')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Efectivo</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">Tarjeta</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="openDetail({{ $sale->id }})"
                                        class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-medium text-stone-600 hover:bg-stone-100 transition">
                                    Ver detalle
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-stone-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                                <p class="mt-2 text-sm text-stone-400">No hay ventas registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($sales->hasPages())
            <div class="mt-4">
                {{ $sales->links() }}
            </div>
        @endif

        {{-- Sale detail modal --}}
        @include('admin.sales.detail-modal')

    </div>

</x-layouts.admin>
