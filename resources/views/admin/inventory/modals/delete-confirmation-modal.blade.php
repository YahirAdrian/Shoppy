<div x-show="showDeleteModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div @click.outside="showDeleteModal = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-bold text-stone-800">Confirmar eliminación</h3>
        <p class="mt-2 text-sm text-stone-600">
            ¿Estás seguro de eliminar <span class="font-semibold" x-text="deleteTarget.name"></span>? Esta acción no se puede deshacer.
        </p>
        <div class="mt-6 flex justify-end gap-3">
            <button @click="showDeleteModal = false" class="rounded-lg border border-stone-300 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 transition">
                Cancelar
            </button>
            <form :action="deleteTarget.type === 'product'
                    ? '{{ url('admin/inventario/productos') }}/' + deleteTarget.id
                    : '{{ url('admin/inventario/categorias') }}/' + deleteTarget.id"
                    method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition">
                    Eliminar
                </button>
            </form>
        </div>
    </div>
</div>