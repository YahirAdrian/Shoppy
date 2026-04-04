{{-- Task create/edit modal --}}
<div x-show="showTaskModal" x-cloak
     class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 py-8"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div @click.outside="showTaskModal = false" class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-bold text-stone-800" x-text="editMode ? 'Editar tarea' : 'Nueva tarea'"></h3>

        <form :action="editMode
                ? '{{ url('admin/tareas') }}/' + form.id
                : '{{ route('admin.tasks.store') }}'"
              method="POST" class="mt-4">
            @csrf
            <template x-if="editMode">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="form.name" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Fecha límite --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Fecha límite</label>
                    <input type="date" name="due_date" x-model="form.due_date"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Repetición --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Repetición</label>
                    <select name="repeat_type" x-model="form.repeat_type"
                            class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="none">Sin repetición</option>
                        <option value="daily">Diario</option>
                        <option value="weekly">Semanal</option>
                        <option value="monthly">Mensual</option>
                    </select>
                </div>

                {{-- Intervalo --}}
                <div x-show="form.repeat_type !== 'none'" x-cloak>
                    <label class="block text-sm font-medium text-stone-700">
                        Cada
                        <span x-show="form.repeat_type === 'daily'" class="font-normal text-stone-500">días</span>
                        <span x-show="form.repeat_type === 'weekly'" class="font-normal text-stone-500">semanas</span>
                        <span x-show="form.repeat_type === 'monthly'" class="font-normal text-stone-500">meses</span>
                    </label>
                    <input type="number" name="repeat_interval" x-model="form.repeat_interval" min="1"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
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
                <button type="button" @click="showTaskModal = false"
                        class="rounded-lg border border-stone-300 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                    <span x-text="editMode ? 'Guardar cambios' : 'Crear tarea'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
