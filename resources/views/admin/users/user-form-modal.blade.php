{{-- User create/edit modal --}}
<div x-show="showUserModal" x-cloak
     class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 py-8"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div @click.outside="showUserModal = false" class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-bold text-stone-800" x-text="editMode ? 'Editar usuario' : 'Nuevo usuario'"></h3>

        <form :action="editMode
                ? '{{ url('admin/usuarios') }}/' + form.id
                : '{{ route('admin.users.store') }}'"
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

                {{-- Correo electrónico --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Correo electrónico <span class="text-red-500">*</span></label>
                    <input type="email" name="email" x-model="form.email" required
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Contraseña --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">
                        Contraseña
                        <template x-if="!editMode"><span class="text-red-500">*</span></template>
                    </label>
                    <input type="password" name="password" x-model="form.password" :required="!editMode" minlength="6"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <template x-if="editMode">
                        <p class="mt-1 text-xs text-stone-400">Dejar vacío para mantener la contraseña actual.</p>
                    </template>
                </div>

                {{-- Confirmar contraseña --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">
                        Confirmar contraseña
                        <template x-if="!editMode"><span class="text-red-500">*</span></template>
                    </label>
                    <input type="password" name="password_confirmation" x-model="form.password_confirmation" :required="!editMode"
                           class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Rol --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700">Rol <span class="text-red-500">*</span></label>
                    <select name="role" x-model="form.role" required
                            class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="seller">Vendedor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                {{-- Estado --}}
                <div>
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" x-bind:checked="form.is_active"
                               class="rounded border-stone-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-stone-700">Usuario activo</span>
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
                <button type="button" @click="showUserModal = false"
                        class="rounded-lg border border-stone-300 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                    <span x-text="editMode ? 'Guardar cambios' : 'Crear usuario'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
