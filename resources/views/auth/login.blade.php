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
    </div>
</x-layouts.guest>
