{{-- POS Sidebar (icon-only) --}}
<aside class="fixed inset-y-0 left-0 z-40 flex w-20 flex-col bg-stone-800 transition-transform duration-200 lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- User avatar --}}
    <div class="flex flex-col items-center gap-2 px-2 py-6 border-b border-stone-700">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-stone-600">
            <img src="{{ asset('icons/user.svg') }}" alt="" class="h-6 w-6" onerror="this.style.display='none'">
        </div>
        <p class="w-full text-center text-xs font-semibold text-white truncate" title="{{ Auth::user()->name }}">
            {{ Auth::user()->name }}
        </p>
        <p class="text-[10px] uppercase tracking-wide text-stone-400">Vendedor</p>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-2 px-2 py-4">
        @php
            $links = [
                ['route' => 'pos.sale', 'label' => 'Venta', 'icon' => 'pos-venta'],
                ['route' => 'pos.search', 'label' => 'Buscar', 'icon' => 'pos-buscar'],
                ['route' => 'pos.status', 'label' => 'Estado', 'icon' => 'pos-estado'],
            ];
        @endphp

        @foreach ($links as $link)
            @php $isActive = request()->routeIs($link['route'] . '*'); @endphp
            <a href="{{ route($link['route']) }}"
               title="{{ $link['label'] }}"
               class="group flex flex-col items-center gap-1 rounded-lg px-2 py-3 text-[11px] font-medium text-white transition-colors
                      {{ $isActive ? 'bg-primary-800 font-bold' : 'hover:bg-primary-500' }}">
                <img src="{{ asset('icons/' . $link['icon'] . '.svg') }}" alt="" class="h-6 w-6 shrink-0">
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Logout --}}
    <div class="border-t border-stone-700 px-2 py-4">
        <form method="POST" action="{{ route('logout') }}" class="flex justify-center">
            @csrf
            <button type="submit" title="Cerrar sesión" class="text-stone-400 hover:text-white transition-colors">
                <img src="{{ asset('icons/logout.svg') }}" alt="Cerrar sesión" class="h-6 w-6">
            </button>
        </form>
    </div>
</aside>
