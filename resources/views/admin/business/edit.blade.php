<x-layouts.admin title="Negocio — Shoppy Adminer">

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="mb-4 flex items-center justify-between rounded-lg bg-green-100 px-4 py-3 text-sm text-green-800">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show"
             class="mb-4 flex items-center justify-between rounded-lg bg-red-100 px-4 py-3 text-sm text-red-800">
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="text-red-600 hover:text-red-800">&times;</button>
        </div>
    @endif

    {{-- Page header --}}
    <h1 class="text-2xl font-bold text-stone-800 uppercase">Negocio</h1>
    <p class="mt-1 text-stone-500">Configuración general de la aplicación</p>

    <form method="POST" action="{{ route('admin.business.update') }}" enctype="multipart/form-data"
          x-data="{
              removeLogo: false,
              logoPreview: null
          }"
          class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- Section 1: Información del negocio --}}
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-stone-800">Información del negocio</h2>
            <p class="mt-1 text-sm text-stone-500">Datos generales de tu negocio</p>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Nombre del negocio --}}
                <div class="sm:col-span-2">
                    <label for="business_name" class="block text-sm font-medium text-stone-700">Nombre del negocio <span class="text-red-500">*</span></label>
                    <input type="text" name="business_name" id="business_name"
                           value="{{ old('business_name', $settings->business_name) }}" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    @error('business_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Logo --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-stone-700">Logo</label>

                    {{-- Current logo --}}
                    @if($settings->logo)
                        <div x-show="!removeLogo" class="mt-2 mb-2 flex items-start gap-3">
                            <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo actual"
                                 class="h-20 w-20 rounded-lg border border-stone-200 object-cover">
                            <div class="flex flex-col gap-1">
                                <p class="text-xs text-stone-500">Logo actual</p>
                                <button type="button" @click="removeLogo = true"
                                        class="text-xs font-medium text-red-600 transition hover:text-red-800">
                                    Eliminar logo
                                </button>
                            </div>
                        </div>
                    @endif

                    <template x-if="removeLogo">
                        <input type="hidden" name="remove_logo" value="1">
                    </template>

                    <input type="file" name="logo" accept="image/*"
                           x-ref="logoInput"
                           @change="
                               const file = $event.target.files[0];
                               if (file) {
                                   const reader = new FileReader();
                                   reader.onload = (e) => { logoPreview = e.target.result; };
                                   reader.readAsDataURL(file);
                                   removeLogo = false;
                               } else {
                                   logoPreview = null;
                               }
                           "
                           class="mt-1 w-full text-sm text-stone-500 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-primary-700 hover:file:bg-primary-100">
                    @error('logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    {{-- New image preview --}}
                    <template x-if="logoPreview">
                        <div class="mt-2 flex items-start gap-3">
                            <img :src="logoPreview" alt="Vista previa" class="h-20 w-20 rounded-lg border border-stone-200 object-cover">
                            <div class="flex flex-col gap-1">
                                <p class="text-xs text-stone-500">Nueva imagen</p>
                                <button type="button" @click="logoPreview = null; $refs.logoInput.value = ''"
                                        class="text-xs font-medium text-red-600 transition hover:text-red-800">
                                    Quitar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Dirección --}}
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-stone-700">Dirección</label>
                    <input type="text" name="address" id="address"
                           value="{{ old('address', $settings->address) }}"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Teléfono --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-stone-700">Teléfono</label>
                    <input type="text" name="phone" id="phone"
                           value="{{ old('phone', $settings->phone) }}"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Correo electrónico --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-stone-700">Correo electrónico</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email', $settings->email) }}"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Configuración de moneda e inventario --}}
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-stone-800">Configuración de moneda e inventario</h2>
            <p class="mt-1 text-sm text-stone-500">Moneda y alertas de stock</p>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Símbolo de moneda --}}
                <div>
                    <label for="currency_symbol" class="block text-sm font-medium text-stone-700">Símbolo de moneda <span class="text-red-500">*</span></label>
                    <input type="text" name="currency_symbol" id="currency_symbol"
                           value="{{ old('currency_symbol', $settings->currency_symbol) }}" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <p class="mt-1 text-xs text-stone-400">Ejemplo: $, Q, €</p>
                    @error('currency_symbol') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Umbral de stock bajo --}}
                <div>
                    <label for="low_stock" class="block text-sm font-medium text-stone-700">Umbral de stock bajo <span class="text-red-500">*</span></label>
                    <input type="number" name="low_stock" id="low_stock" min="1"
                           value="{{ old('low_stock', $settings->low_stock) }}" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <p class="mt-1 text-xs text-stone-400">Productos con stock igual o menor a este valor se marcarán como stock bajo</p>
                    @error('low_stock') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Ticket de venta --}}
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-stone-800">Ticket de venta</h2>
            <p class="mt-1 text-sm text-stone-500">Texto personalizado para los tickets impresos</p>

            <div class="mt-4 space-y-4">
                {{-- Encabezado del ticket --}}
                <div>
                    <label for="receipt_header" class="block text-sm font-medium text-stone-700">Encabezado del ticket</label>
                    <textarea name="receipt_header" id="receipt_header" rows="3"
                              class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">{{ old('receipt_header', $settings->receipt_header) }}</textarea>
                    @error('receipt_header') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Pie del ticket --}}
                <div>
                    <label for="receipt_footer" class="block text-sm font-medium text-stone-700">Pie del ticket</label>
                    <textarea name="receipt_footer" id="receipt_footer" rows="3"
                              class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">{{ old('receipt_footer', $settings->receipt_footer) }}</textarea>
                    @error('receipt_footer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end">
            <button type="submit"
                    class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-primary-700">
                Guardar cambios
            </button>
        </div>
    </form>

</x-layouts.admin>
