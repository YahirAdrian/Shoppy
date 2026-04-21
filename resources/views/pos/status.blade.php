<x-layouts.pos title="Estado de sesión - Shoppy Sales">

    {{-- Hidden logout form --}}
    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

    <div x-data="posStatus({
            salesApiBase: '{{ route('pos.api.sales.store') }}',
            adminAuthUrl: '{{ route('pos.api.admin-auth') }}',
            logoutUrl: '{{ route('logout') }}',
            currency: @js($currency),
            totalSold: @js($totalSold),
            sales: @js($salesData)
         })" x-init="init()" x-cloak>

        {{-- Page header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold uppercase text-stone-800">Estado de sesión</h1>
            <p class="mt-1 text-stone-500">Resumen del día de hoy</p>
        </div>

        {{-- Stats grid --}}
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-stone-500">Vendedor</p>
                <p class="mt-1 text-lg font-bold text-stone-800">{{ $sellerName }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-stone-500">Ventas realizadas</p>
                <p class="mt-1 text-3xl font-bold text-primary-700" x-text="sales.length"></p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-stone-500">Total recaudado</p>
                <p class="mt-1 text-3xl font-bold text-primary-700" x-text="money(totalSold)"></p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-stone-500">Ticket promedio</p>
                <p class="mt-1 text-3xl font-bold text-stone-800">
                    {{ $currency }}{{ number_format($avgTicket, 2) }}
                </p>
            </div>
        </div>

        {{-- Sales history table --}}
        <div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                <h2 class="font-semibold text-stone-800">Historial de ventas de hoy</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Venta #</th>
                            <th class="px-4 py-3 text-left">Hora</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                            <th class="px-4 py-3 text-right">Descuento</th>
                            <th class="px-4 py-3 text-left">Pago</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-left">Nota</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        <template x-if="sales.length === 0">
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-stone-400">
                                    No hay ventas registradas hoy
                                </td>
                            </tr>
                        </template>
                        <template x-for="sale in sales" :key="sale.id">
                            <tr class="hover:bg-stone-50">
                                <td class="px-4 py-3 font-mono font-semibold text-primary-700" x-text="'#' + sale.id"></td>
                                <td class="px-4 py-3 text-stone-600" x-text="formatTime(sale.created_at)"></td>
                                <td class="px-4 py-3 text-right text-stone-700" x-text="money(sale.subtotal)"></td>
                                <td class="px-4 py-3 text-right text-red-600"
                                    x-text="sale.discount_amount > 0 ? '-' + money(sale.discount_amount) : '—'"></td>
                                <td class="px-4 py-3 text-stone-600" x-text="paymentLabel(sale.payment_method)"></td>
                                <td class="px-4 py-3 text-right font-semibold text-stone-800" x-text="money(sale.total)"></td>
                                <td class="max-w-[140px] truncate px-4 py-3 text-stone-500"
                                    x-text="sale.note || '—'"></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        {{-- Preview --}}
                                        <button type="button" @click="openPreview(sale)"
                                                class="rounded bg-stone-100 px-2 py-1 text-xs font-medium text-stone-700 hover:bg-stone-200">
                                            Ver
                                        </button>
                                        {{-- Delete (admin only) --}}
                                        <button type="button" @click="confirmDelete(sale)"
                                                x-show="isAdminUnlocked"
                                                class="rounded bg-red-50 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-100">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Admin section --}}
        <div class="rounded-xl bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-stone-100 px-6 py-4">
                <button type="button" @click="isAdminUnlocked ? lockAdmin() : openAdminModal()"
                        :class="isAdminUnlocked ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-600'"
                        class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors hover:opacity-80">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span x-text="isAdminUnlocked ? 'Admin autorizado — Bloquear' : 'Admin'"></span>
                </button>
                <p class="text-sm text-stone-500" x-show="isAdminUnlocked">
                    Las operaciones de administrador están disponibles
                </p>
                <p class="text-sm text-stone-400" x-show="!isAdminUnlocked">
                    Requiere autorización de administrador
                </p>
            </div>

            <div class="px-6 py-5" x-show="isAdminUnlocked">

                {{-- Money withdrawal --}}
                <h3 class="mb-3 text-sm font-semibold text-stone-700">Retiro de dinero</h3>

                <div class="mb-4 flex items-end gap-3">
                    <div>
                        <label class="mb-1 block text-xs text-stone-500">Monto a retirar</label>
                        <div class="flex items-center gap-1">
                            <span class="text-stone-500">{{ $currency }}</span>
                            <input type="number" step="0.01" min="0" x-model="withdrawalInput"
                                   @keydown.enter="addWithdrawal()"
                                   class="w-36 rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        </div>
                    </div>
                    <button type="button" @click="addWithdrawal()"
                            class="rounded-lg bg-stone-700 px-4 py-2 text-sm font-medium text-white hover:bg-stone-800">
                        Registrar retiro
                    </button>
                </div>

                <p x-show="withdrawalError" x-text="withdrawalError"
                   class="mb-3 text-sm text-red-600"></p>

                <div class="mb-4 flex gap-8 text-sm">
                    <div>
                        <p class="text-stone-500">Total recaudado</p>
                        <p class="text-lg font-bold text-stone-800" x-text="money(totalSold)"></p>
                    </div>
                    <div>
                        <p class="text-stone-500">Total retirado</p>
                        <p class="text-lg font-bold"
                           :class="canEndSession() ? 'text-green-700' : 'text-stone-800'"
                           x-text="money(totalWithdrawn)"></p>
                    </div>
                    <div>
                        <p class="text-stone-500">Pendiente</p>
                        <p class="text-lg font-bold text-red-600"
                           x-text="money(Math.max(0, totalSold - totalWithdrawn))"></p>
                    </div>
                </div>

                <div x-show="withdrawals.length > 0" class="mb-4">
                    <p class="mb-1 text-xs text-stone-400">Retiros registrados</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(w, i) in withdrawals" :key="i">
                            <span class="rounded bg-stone-100 px-2 py-0.5 text-xs text-stone-600"
                                  x-text="money(w)"></span>
                        </template>
                    </div>
                </div>

                {{-- End session --}}
                <div class="border-t border-stone-100 pt-4">
                    <button type="button" @click="endSession()"
                            :disabled="!canEndSession()"
                            class="rounded-lg px-5 py-2 text-sm font-semibold text-white transition-colors
                                   disabled:cursor-not-allowed disabled:bg-stone-300
                                   enabled:bg-red-600 enabled:hover:bg-red-700">
                        Terminar sesión
                    </button>
                    <p x-show="!canEndSession()" class="mt-1 text-xs text-stone-400">
                        Retire todo el dinero para poder terminar la sesión
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Preview modal ────────────────────────────────────────────── --}}
        <div x-show="showPreviewModal" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

            <div class="w-full max-w-2xl rounded-xl bg-white shadow-xl"
                 @click.outside="closePreview()">

                <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
                    <h3 class="font-semibold text-stone-800">
                        Detalle de venta <span x-show="previewSale" x-text="'#' + (previewSale?.id ?? '')"></span>
                    </h3>
                    <button type="button" @click="closePreview()" class="text-stone-400 hover:text-stone-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="max-h-[70vh] overflow-y-auto px-6 py-5">

                    {{-- Loading --}}
                    <div x-show="loadingPreview" class="py-8 text-center text-stone-400">Cargando…</div>

                    {{-- Error --}}
                    <p x-show="previewError" x-text="previewError" class="text-sm text-red-600"></p>

                    {{-- Content --}}
                    <template x-if="previewSale">
                        <div>
                            <div class="mb-4 grid grid-cols-2 gap-x-6 gap-y-1 text-sm">
                                <div>
                                    <span class="text-stone-500">Fecha:</span>
                                    <span class="ml-1 text-stone-800" x-text="new Date(previewSale.created_at).toLocaleString('es-MX')"></span>
                                </div>
                                <div>
                                    <span class="text-stone-500">Pago:</span>
                                    <span class="ml-1 text-stone-800" x-text="paymentLabel(previewSale.payment_method)"></span>
                                </div>
                                <template x-if="previewSale.customer_name">
                                    <div>
                                        <span class="text-stone-500">Cliente:</span>
                                        <span class="ml-1 text-stone-800" x-text="previewSale.customer_name"></span>
                                    </div>
                                </template>
                                <template x-if="previewSale.note">
                                    <div>
                                        <span class="text-stone-500">Nota:</span>
                                        <span class="ml-1 text-stone-800" x-text="previewSale.note"></span>
                                    </div>
                                </template>
                            </div>

                            <table class="w-full text-sm">
                                <thead class="border-b border-stone-200 text-xs uppercase text-stone-500">
                                    <tr>
                                        <th class="pb-2 text-left">Producto</th>
                                        <th class="pb-2 text-right">Cant.</th>
                                        <th class="pb-2 text-right">P.U.</th>
                                        <th class="pb-2 text-right">Desc.</th>
                                        <th class="pb-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100">
                                    <template x-for="(item, i) in previewSale.items" :key="i">
                                        <tr>
                                            <td class="py-2 text-stone-800" x-text="item.product_name"></td>
                                            <td class="py-2 text-right text-stone-600" x-text="item.quantity"></td>
                                            <td class="py-2 text-right text-stone-600" x-text="money(item.unit_price)"></td>
                                            <td class="py-2 text-right text-red-500"
                                                x-text="item.discount_amount > 0 ? '-' + money(item.discount_amount) : '—'"></td>
                                            <td class="py-2 text-right font-semibold text-stone-800" x-text="money(item.subtotal)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="border-t border-stone-200 text-sm font-semibold">
                                    <tr>
                                        <td colspan="4" class="pt-3 text-right text-stone-600">Subtotal</td>
                                        <td class="pt-3 text-right text-stone-800" x-text="money(previewSale.subtotal)"></td>
                                    </tr>
                                    <template x-if="previewSale.discount_amount > 0">
                                        <tr>
                                            <td colspan="4" class="py-1 text-right text-stone-600">Descuento</td>
                                            <td class="py-1 text-right text-red-600" x-text="'-' + money(previewSale.discount_amount)"></td>
                                        </tr>
                                    </template>
                                    <tr class="text-base">
                                        <td colspan="4" class="pt-1 text-right text-stone-800">Total</td>
                                        <td class="pt-1 text-right text-primary-700" x-text="money(previewSale.total)"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="pt-1 text-right text-stone-500">Recibido</td>
                                        <td class="pt-1 text-right text-stone-700" x-text="money(previewSale.amount_tendered)"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right text-stone-500">Cambio</td>
                                        <td class="text-right text-stone-700" x-text="money(previewSale.change_given)"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end border-t border-stone-200 px-6 py-4">
                    <button type="button" @click="closePreview()"
                            class="rounded-lg bg-stone-100 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-200">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Delete confirm modal ─────────────────────────────────────── --}}
        <div x-show="showDeleteConfirm" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

            <div class="w-full max-w-sm rounded-xl bg-white shadow-xl">
                <div class="border-b border-stone-200 px-6 py-4">
                    <h3 class="font-semibold text-red-700">Eliminar venta</h3>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-stone-700">
                        ¿Está seguro de eliminar la venta
                        <span class="font-semibold" x-text="'#' + (saleToDelete?.id ?? '')"></span>?
                        Esta acción restaurará el stock de los productos.
                    </p>
                    <p x-show="deleteError" x-text="deleteError"
                       class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"></p>
                </div>
                <div class="flex justify-end gap-2 border-t border-stone-200 px-6 py-4">
                    <button type="button" @click="showDeleteConfirm = false; saleToDelete = null"
                            :disabled="deleting"
                            class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">
                        Cancelar
                    </button>
                    <button type="button" @click="executeDelete()"
                            :disabled="deleting"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50">
                        <span x-show="!deleting">Sí, eliminar</span>
                        <span x-show="deleting">Eliminando…</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Admin auth modal ─────────────────────────────────────────── --}}
        <div x-show="showAdminModal" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

            <div class="w-full max-w-sm rounded-xl bg-white shadow-xl"
                 @click.outside="showAdminModal = false">

                <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
                    <h3 class="font-semibold text-stone-800">Autorización de administrador</h3>
                    <button type="button" @click="showAdminModal = false" class="text-stone-400 hover:text-stone-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 px-6 py-5">
                    <p class="text-sm text-stone-600">
                        Ingrese las credenciales de un administrador para habilitar operaciones admin.
                        La autorización expira en 15 minutos.
                    </p>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">Correo del administrador</label>
                        <input type="email" x-model="adminEmail"
                               @keydown.enter="submitAdminAuth()"
                               class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">Contraseña</label>
                        <input type="password" x-model="adminPassword"
                               @keydown.enter="submitAdminAuth()"
                               class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    </div>

                    <p x-show="adminError" x-text="adminError"
                       class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"></p>
                </div>

                <div class="flex justify-end gap-2 border-t border-stone-200 px-6 py-4">
                    <button type="button" @click="showAdminModal = false"
                            class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">
                        Cancelar
                    </button>
                    <button type="button" @click="submitAdminAuth()"
                            :disabled="adminLoading"
                            class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50">
                        <span x-show="!adminLoading">Autorizar</span>
                        <span x-show="adminLoading">Verificando…</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-layouts.pos>
