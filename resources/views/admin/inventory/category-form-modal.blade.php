{{-- Category create/edit modal --}}
<div x-show="showCategoryModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div @click.outside="showCategoryModal = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-bold text-stone-800" x-text="categoryEditMode ? 'Editar categoría' : 'Nueva categoría'"></h3>

        <form :action="categoryEditMode
                ? '{{ url('admin/inventario/categorias') }}/' + categoryForm.id
                : '{{ route('admin.categories.store') }}'"
              method="POST" class="mt-4">
            @csrf
            <template x-if="categoryEditMode">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="categoryForm.name" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Descripción</label>
                    <textarea name="description" x-model="categoryForm.description" rows="3"
                              class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"></textarea>
                </div>

                {{-- Activa --}}
                <div>
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" x-bind:checked="categoryForm.is_active"
                               class="rounded border-stone-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-stone-700">Categoría activa</span>
                    </label>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="showCategoryModal = false"
                        class="rounded-lg border border-stone-300 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                    <span x-text="categoryEditMode ? 'Guardar cambios' : 'Crear categoría'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
