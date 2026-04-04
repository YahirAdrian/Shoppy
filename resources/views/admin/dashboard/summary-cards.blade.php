<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

    {{-- Ventas --}}
    <div class="rounded-xl bg-gradient-to-br from-primary-600 to-primary-800 p-5 text-white shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-wide opacity-90">Ventas</h2>
            <select class="rounded-md bg-white/20 px-2 py-1 text-xs text-white backdrop-blur focus:outline-none">
                <option value="daily" selected>Hoy</option>
                <option value="weekly">Semanal</option>
                <option value="monthly">Mensual</option>
            </select>
        </div>
        <p class="mt-3 text-3xl font-bold">24</p>
        <p class="mt-1 text-sm opacity-80">$12,480.00 en ingresos</p>
        <hr class="my-3 border-white/20">
        <p class="mb-2 text-xs font-semibold uppercase opacity-70">Ventas recientes</p>
        <ul class="space-y-1 text-sm">
            <li class="flex justify-between"><span>Venta #1042</span><span>$520.00</span></li>
            <li class="flex justify-between"><span>Venta #1041</span><span>$345.50</span></li>
            <li class="flex justify-between"><span>Venta #1040</span><span>$189.00</span></li>
        </ul>
    </div>

    {{-- Productos --}}
    <div class="rounded-xl bg-gradient-to-br from-green-500 to-green-700 p-5 text-white shadow">
        <h2 class="text-sm font-semibold uppercase tracking-wide opacity-90">Productos</h2>
        <div class="mt-3 grid grid-cols-2 gap-4">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase opacity-70">Más vendidos</p>
                <ul class="space-y-1 text-sm">
                    <li>1. Coca-Cola 600ml</li>
                    <li>2. Pan Bimbo</li>
                    <li>3. Sabritas Original</li>
                </ul>
            </div>
            <div>
                <p class="mb-2 text-xs font-semibold uppercase opacity-70">Stock bajo</p>
                <ul class="space-y-1 text-sm">
                    <li class="flex justify-between"><span>Leche Lala</span><span class="font-bold">2</span></li>
                    <li class="flex justify-between"><span>Huevos</span><span class="font-bold">3</span></li>
                    <li class="flex justify-between"><span>Azúcar 1kg</span><span class="font-bold">4</span></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Pendientes --}}
    <div class="rounded-xl bg-gradient-to-br from-accent-400 to-accent-600 p-5 text-white shadow">
        <h2 class="text-sm font-semibold uppercase tracking-wide opacity-90">Pendientes</h2>
        <p class="mt-3 text-3xl font-bold">5</p>
        <p class="mt-1 text-sm opacity-80">acciones pendientes</p>
        <hr class="my-3 border-white/20">
        <ul class="space-y-2 text-sm">
            <li class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-white/80"></span>
                3 productos con stock bajo
            </li>
            <li class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-white/80"></span>
                1 reporte de vendedor
            </li>
            <li class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-white/80"></span>
                1 tarea programada vencida
            </li>
        </ul>
    </div>
</div>
