{{-- Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-stone-800 transition-transform duration-200 lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- Logo --}}
    <div class="flex flex-col items-center gap-3 px-6 py-8">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-700">
            <img src="{{ asset('shoppy-logo-white.svg') }}" alt="Shoppy" class="h-10 w-10">
        </div>
        <span class="text-lg font-semibold text-white">Shoppy Adminer</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1 px-3">
        @php
            $links = [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                ['route' => 'admin.sales.index', 'label' => 'Ventas', 'icon' => 'ventas'],
                ['route' => 'admin.inventory.index', 'label' => 'Inventario', 'icon' => 'inventario'],
                ['route' => 'admin.reports.index', 'label' => 'Reportes', 'icon' => 'reportes'],
                ['route' => 'admin.business.edit', 'label' => 'Negocio', 'icon' => 'negocio'],
                ['route' => 'admin.users.index', 'label' => 'Usuarios', 'icon' => 'usuarios'],
                ['route' => 'admin.tasks.index', 'label' => 'Tareas', 'icon' => 'tareas'],
            ];
        @endphp

        @foreach ($links as $link)
            @php
                $isActive = request()->routeIs($link['route'] . '*');
                $exists = \Illuminate\Support\Facades\Route::has($link['route']);
            @endphp

            @if ($exists)
                <a href="{{ route($link['route']) }}"
                   class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors text-white
                          {{ $isActive
                              ? 'bg-primary-800  font-bold'
                              : '' }}">
            @else
                <span class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium text-white cursor-pointer hover:bg-primary-500 transition-colors">
            @endif
                    <img src="{{ asset('icons/' . $link['icon'] . '.svg') }}" alt="" class="h-5 w-5 shrink-0">
                    {{ $link['label'] }}
            @if ($exists)
                </a>
            @else
                </span>
            @endif
        @endforeach
    </nav>

    {{-- User footer --}}
    <div class="border-t border-stone-700 px-4 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-stone-600">
                <img src="{{ asset('icons/user.svg') }}" alt="" class="h-5 w-5" onerror="this.style.display='none'">
            </div>
            <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-stone-400">Administrador</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Cerrar sesión" class="text-stone-400 hover:text-white transition-colors">
                    <img src="{{ asset('icons/logout.svg') }}" alt="Cerrar sesión" class="h-5 w-5">
                </button>
            </form>
        </div>
    </div>
</aside>
