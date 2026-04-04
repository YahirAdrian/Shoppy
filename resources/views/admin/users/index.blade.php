<x-layouts.admin title="Usuarios — Shoppy Adminer">

    {{-- Main container --}}
    <div x-data="{
        showUserModal: false,
        editMode: false,
        form: {
            id: null,
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            role: 'seller',
            is_active: true,
        },
        resetForm() {
            this.form = {
                id: null,
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role: 'seller',
                is_active: true,
            };
        },
        openEdit(user) {
            this.editMode = true;
            this.form = {
                id: user.id,
                name: user.name,
                email: user.email,
                password: '',
                password_confirmation: '',
                role: user.role,
                is_active: user.is_active,
            };
            this.showUserModal = true;
        }
    }">

        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-stone-800 uppercase">Usuarios</h1>
                <p class="mt-1 text-stone-500">Administración de usuarios del sistema</p>
            </div>
            <button @click="showUserModal = true; editMode = false; resetForm()"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                + Nuevo usuario
            </button>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mt-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Users table --}}
        <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3 text-center">Rol</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3">Fecha de registro</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="px-4 py-3 font-medium text-stone-800">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="ml-1 text-xs text-stone-400">(tú)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-stone-600">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-700">Admin</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-accent-100 px-2.5 py-0.5 text-xs font-medium text-accent-700">Vendedor</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($user->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Activo</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-stone-600">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button x-ref="trigger" @click="open = !open" class="rounded-lg border border-stone-300 p-1.5 text-stone-500 hover:bg-stone-100 transition">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="4" r="2"/><circle cx="10" cy="10" r="2"/><circle cx="10" cy="16" r="2"/></svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-cloak
                                         x-init="$watch('open', val => {
                                             if (val) {
                                                 const rect = $refs.trigger.getBoundingClientRect();
                                                 $el.style.top = (rect.bottom + 4) + 'px';
                                                 $el.style.left = (rect.right - $el.offsetWidth -120) + 'px';
                                             }
                                         })"
                                         class="fixed z-50 w-40 rounded-lg border border-stone-200 bg-white py-1 shadow-lg">
                                        <button @click="open = false; openEdit(@js($user->only(['id', 'name', 'email', 'role', 'is_active'])))"
                                                class="w-full px-4 py-2 text-left text-sm text-stone-700 hover:bg-stone-50 transition">
                                            Editar
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="w-full px-4 py-2 text-left text-sm {{ $user->is_active ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }} transition">
                                                    {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-stone-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                                <p class="mt-2 text-sm text-stone-400">No hay usuarios registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- User create/edit modal --}}
        @include('admin.users.user-form-modal')

    </div>

</x-layouts.admin>
