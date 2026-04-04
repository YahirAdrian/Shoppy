<x-layouts.admin title="Tareas — Shoppy Adminer">

    <div x-data="{
        showTaskModal: false,
        editMode: false,
        form: {
            id: null,
            name: '',
            due_date: '',
            repeat_type: 'none',
            repeat_interval: 1,
        },
        resetForm() {
            this.form = {
                id: null,
                name: '',
                due_date: '',
                repeat_type: 'none',
                repeat_interval: 1,
            };
        },
        openEdit(task) {
            this.editMode = true;
            this.form = {
                id: task.id,
                name: task.name,
                due_date: task.due_date ?? '',
                repeat_type: task.repeat_type,
                repeat_interval: task.repeat_interval ?? 1,
            };
            this.showTaskModal = true;
        }
    }">

        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-stone-800 uppercase">Tareas</h1>
                <p class="mt-1 text-stone-500">Lista de pendientes del administrador</p>
            </div>
            <button @click="showTaskModal = true; editMode = false; resetForm()"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                + Nueva tarea
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

        {{-- Pending tasks --}}
        <div class="mt-6">
            <h2 class="text-lg font-semibold text-stone-700">Pendientes</h2>
            <div class="mt-3 space-y-2">
                @forelse($pending as $task)
                    <div class="flex items-center gap-3 rounded-xl border border-stone-200 bg-white px-4 py-3 shadow-sm">
                        {{-- Toggle checkbox --}}
                        <form action="{{ route('admin.tasks.toggle', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="flex-shrink-0 h-5 w-5 rounded border-2 border-stone-300 hover:border-primary-500 transition" title="Completar"></button>
                        </form>

                        {{-- Task info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-stone-800 truncate">{{ $task->name }}</p>
                            <div class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-stone-500">
                                @if($task->due_date)
                                    <span class="{{ $task->isOverdue() ? 'text-red-500 font-medium' : '' }}">
                                        {{ $task->isOverdue() ? 'Vencida: ' : '' }}{{ $task->due_date->format('d/m/Y') }}
                                    </span>
                                @endif
                                @if($task->isRecurring())
                                    <span class="inline-flex items-center rounded-full bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700">
                                        @if($task->repeat_interval == 1)
                                            {{ match($task->repeat_type) { 'daily' => 'Diario', 'weekly' => 'Semanal', 'monthly' => 'Mensual' } }}
                                        @else
                                            Cada {{ $task->repeat_interval }}
                                            {{ match($task->repeat_type) { 'daily' => 'días', 'weekly' => 'semanas', 'monthly' => 'meses' } }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Kebab menu --}}
                        <div x-data="{ open: false }" class="relative">
                            <button x-ref="trigger" @click="open = !open" class="rounded-lg border border-stone-300 p-1.5 text-stone-500 hover:bg-stone-100 transition">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="4" r="2"/><circle cx="10" cy="10" r="2"/><circle cx="10" cy="16" r="2"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 x-init="$watch('open', val => {
                                     if (val) {
                                         const rect = $refs.trigger.getBoundingClientRect();
                                         $el.style.top = (rect.bottom + 4) + 'px';
                                         $el.style.left = (rect.right - $el.offsetWidth - 120) + 'px';
                                     }
                                 })"
                                 class="fixed z-50 w-36 rounded-lg border border-stone-200 bg-white py-1 shadow-lg">
                                <button @click="open = false; openEdit(@js($task->only(['id', 'name', 'due_date', 'repeat_type', 'repeat_interval'])))"
                                        class="w-full px-4 py-2 text-left text-sm text-stone-700 hover:bg-stone-50 transition">
                                    Editar
                                </button>
                                <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 transition">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50 px-4 py-8 text-center">
                        <p class="text-sm text-stone-400">No hay tareas pendientes.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Completed tasks --}}
        @if($completed->count())
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-stone-700">Completadas</h2>
                <div class="mt-3 space-y-2">
                    @foreach($completed as $task)
                        <div class="flex items-center gap-3 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                            {{-- Toggle checkbox (filled) --}}
                            <form action="{{ route('admin.tasks.toggle', $task) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="flex-shrink-0 h-5 w-5 rounded border-2 border-primary-500 bg-primary-500 flex items-center justify-center transition hover:bg-primary-400" title="Desmarcar">
                                    <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>

                            {{-- Task info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-stone-400 line-through truncate">{{ $task->name }}</p>
                                <p class="mt-0.5 text-xs text-stone-400">
                                    Completada {{ $task->completed_at->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            {{-- Delete --}}
                            <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg p-1.5 text-stone-400 hover:text-red-500 transition" title="Eliminar">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Task form modal --}}
        @include('admin.tasks.task-form-modal')

    </div>

</x-layouts.admin>
