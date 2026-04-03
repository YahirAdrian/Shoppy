{{-- Product create/edit modal --}}
<div x-show="showProductModal" x-cloak
     class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 py-8"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div @click.outside="showProductModal = false" class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-bold text-stone-800" x-text="productEditMode ? 'Editar producto' : 'Nuevo producto'"></h3>

        <form :action="productEditMode
                ? '{{ url('admin/inventario/productos') }}/' + productForm.id
                : '{{ route('admin.products.store') }}'"
              method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <template x-if="productEditMode">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Nombre --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-stone-700">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="productForm.name" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- SKU --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">SKU <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" x-model="productForm.sku" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Código de barras --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Código de barras</label>
                    <input type="text" name="barcode" x-model="productForm.barcode"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Categoría --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Categoría <span class="text-red-500">*</span></label>
                    <select name="category_id" x-model="productForm.category_id" required
                            class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="">Seleccionar...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Unidad --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Unidad <span class="text-red-500">*</span></label>
                    <input type="text" name="unit" x-model="productForm.unit" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Precio de costo --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Precio de costo <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-primary-600 font-bold">{{ $currency }}</span>
                        <input type="number" name="cost_price" x-model="productForm.cost_price" step="0.01" min="0" required
                               class="w-full rounded-lg border border-stone-300 py-2 pl-7 pr-3 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    </div>
                </div>

                {{-- Precio de venta --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Precio de venta <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-primary-600 font-bold">{{ $currency }}</span>
                        <input type="number" name="selling_price" x-model="productForm.selling_price" step="0.01" min="0" required
                               class="w-full rounded-lg border border-stone-300 py-2 pl-7 pr-3 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    </div>
                </div>

                {{-- Stock (solo en creación) --}}
                <div x-show="!productEditMode">
                    <label class="block text-sm font-medium text-stone-700">Stock inicial <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" x-model="productForm.stock" step="0.01" min="0" x-bind:required="!productEditMode"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Alerta de stock bajo --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Alerta stock bajo</label>
                    <input type="number" name="low_stock_alert" x-model="productForm.low_stock_alert" step="0.01" min="0"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Descripción --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-stone-700">Descripción</label>
                    <textarea name="description" x-model="productForm.description" rows="2"
                              class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"></textarea>
                </div>

                {{-- Imagen --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-stone-700">Imagen</label>
                    <template x-if="productEditMode && productForm.image && !productForm.remove_image">
                        <div class="mt-1 mb-2 flex items-start gap-3">
                            <img :src="'/storage/' + productForm.image" alt="Imagen actual" class="h-24 w-24 rounded-lg border border-stone-200 object-cover">
                            <div class="flex flex-col gap-1">
                                <p class="text-xs text-stone-500">Imagen actual</p>
                                <button type="button" @click="productForm.remove_image = true"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium transition">
                                    Quitar imagen
                                </button>
                            </div>
                        </div>
                    </template>
                    <template x-if="productEditMode && productForm.remove_image">
                        <input type="hidden" name="remove_image" value="1">
                    </template>
                    <input type="file" name="image" accept="image/*"
                           x-ref="imageInput"
                           @change="
                               const file = $event.target.files[0];
                               if (file) {
                                   const reader = new FileReader();
                                   reader.onload = (e) => { productForm.image_preview = e.target.result; };
                                   reader.readAsDataURL(file);
                               } else {
                                   productForm.image_preview = null;
                               }
                           "
                           class="mt-1 w-full text-sm text-stone-500 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-primary-700 hover:file:bg-primary-100">
                    <template x-if="productForm.image_preview">
                        <div class="mt-2 flex items-start gap-3">
                            <img :src="productForm.image_preview" alt="Vista previa" class="h-24 w-24 rounded-lg border border-stone-200 object-cover">
                            <div class="flex flex-col gap-1">
                                <p class="text-xs text-stone-500">Nueva imagen</p>
                                <button type="button" @click="productForm.image_preview = null; $refs.imageInput.value = ''"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium transition">
                                    Quitar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Activo --}}
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" x-bind:checked="productForm.is_active"
                               class="rounded border-stone-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-stone-700">Producto activo</span>
                    </label>
                </div>
            </div>

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="mt-4 rounded-lg bg-red-50 p-3">
                    <ul class="list-disc pl-4 text-sm text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Actions --}}
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="showProductModal = false"
                        class="rounded-lg border border-stone-300 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                    <span x-text="productEditMode ? 'Guardar cambios' : 'Crear producto'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
