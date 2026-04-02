<section class="mt-8">
    <h2 class="text-lg font-bold text-stone-800">Acciones</h2>

    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Tareas pendientes --}}
        <div class="rounded-xl bg-white p-5 shadow">
            <h3 class="mb-4 text-sm font-semibold text-stone-600 uppercase">Tareas pendientes</h3>
            <ul class="divide-y divide-stone-100">
                @php
                    $tasks = [
                        ['name' => 'Actualizar inventario', 'link' => 'Inventario', 'color' => 'primary', 'date' => '01 Abr 2026'],
                        ['name' => 'Generar reportes mensuales', 'link' => 'Reportes', 'color' => 'accent', 'date' => '01 Abr 2026'],
                        ['name' => 'Procesar devoluciones', 'link' => 'Reportes', 'color' => 'accent', 'date' => '02 Abr 2026'],
                        ['name' => 'Revisar precios de temporada', 'link' => 'Inventario', 'color' => 'primary', 'date' => '05 Abr 2026'],
                    ];
                @endphp
                @foreach ($tasks as $task)
                    <li class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" class="h-4 w-4 rounded border-stone-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <p class="text-sm font-medium text-stone-700">{{ $task['name'] }}</p>
                                <p class="text-xs text-stone-400">{{ $task['date'] }}</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-{{ $task['color'] }}-100 px-2.5 py-0.5 text-xs font-medium text-{{ $task['color'] }}-700">
                            {{ $task['link'] }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Tareas próximas --}}
        <div class="rounded-xl bg-white p-5 shadow">
            <h3 class="mb-4 text-sm font-semibold text-stone-600 uppercase">Próximas tareas</h3>
            <ul class="divide-y divide-stone-100">
                @php
                    $upcoming = [
                        ['name' => 'Pago a proveedor — Bimbo', 'date' => '07 Abr 2026', 'repeat' => 'Mensual'],
                        ['name' => 'Conteo de inventario físico', 'date' => '10 Abr 2026', 'repeat' => 'Semanal'],
                        ['name' => 'Revisión de caducidades', 'date' => '15 Abr 2026', 'repeat' => 'Quincenal'],
                    ];
                @endphp
                @foreach ($upcoming as $task)
                    <li class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-medium text-stone-700">{{ $task['name'] }}</p>
                            <p class="text-xs text-stone-400">{{ $task['date'] }}</p>
                        </div>
                        <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-500">
                            {{ $task['repeat'] }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
