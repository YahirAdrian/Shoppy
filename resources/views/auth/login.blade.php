<x-layouts.guest title="Iniciar sesión — Shoppy">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">

            <!-- Logo / App name -->
            <div class="text-center mb-8">
                <span class="text-3xl font-bold text-primary-600 tracking-tight">Shoppy</span>
                <p class="text-dark-400 text-sm mt-1">Inicia sesión para continuar</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <!-- Email -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-dark-800 mb-1">
                        Correo electrónico
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                        class="w-full px-4 py-2.5 rounded-lg border @error('email') border-red-400 bg-red-50 @else border-dark-200 @enderror text-dark-900 placeholder-dark-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                        placeholder="correo@ejemplo.com"
                    >
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-dark-800 mb-1">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        class="w-full px-4 py-2.5 rounded-lg border @error('password') border-red-400 bg-red-50 @else border-dark-200 @enderror text-dark-900 placeholder-dark-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me -->
                <div class="flex items-center mb-6">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="w-4 h-4 rounded border-dark-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                    >
                    <label for="remember" class="ml-2 text-sm text-dark-600 cursor-pointer">
                        Recordarme
                    </label>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    Iniciar sesión
                </button>
            </form>
        </div>
        <!-- Guest credentials -->
        <div x-data="guestCredentials('email', 'password')" class="mt-4">
            <button
                type="button"
                @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl bg-white/60 border border-dark-200 text-sm text-dark-500 hover:bg-white hover:text-dark-700 transition"
            >
                <span class="font-medium">Credenciales de demostración</span>
                <svg
                    :class="open ? 'rotate-180' : ''"
                    class="w-4 h-4 transition-transform"
                    fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-cloak x-transition class="mt-2 bg-white rounded-xl border border-dark-200 shadow-sm overflow-hidden">
                <div class="px-4 py-2 bg-dark-50 border-b border-dark-100">
                    <p class="text-xs text-dark-400 font-medium uppercase tracking-wide">Acceso rápido</p>
                </div>

                <!-- Admin row -->
                <button
                    type="button"
                    @click="fill('admin@shoppy.local', '1234')"
                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-primary-50 transition text-left border-b border-dark-100"
                >
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-700 text-xs font-bold shrink-0">A</span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-dark-800">Administrador</p>
                        <p class="text-xs text-dark-400 truncate">admin@shoppy.local &nbsp;·&nbsp; 1234</p>
                    </div>
                    <span class="ml-auto text-xs text-primary-500 font-medium shrink-0">Usar</span>
                </button>

                <!-- Seller row -->
                <button
                    type="button"
                    @click="fill('maria@shoppy.local', '1234')"
                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-accent-50 transition text-left"
                >
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-accent-100 text-accent-700 text-xs font-bold shrink-0">M</span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-dark-800">María (Vendedora)</p>
                        <p class="text-xs text-dark-400 truncate">maria@shoppy.local &nbsp;·&nbsp; 1234</p>
                    </div>
                    <span class="ml-auto text-xs text-accent-500 font-medium shrink-0">Usar</span>
                </button>
            </div>
        </div>
    </div>
</x-layouts.guest>
