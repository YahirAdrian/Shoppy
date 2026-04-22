<section class="mt-8">
    <h2 class="text-lg font-bold text-stone-800">Acciones</h2>

    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Tareas pendientes / vencidas --}}
        <div class="rounded-xl bg-white p-5 shadow">
            <h3 class="mb-4 text-sm font-semibold text-stone-600 uppercase">Tareas pendientes</h3>
            @forelse ($pendingTasks as $task)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-stone-100' : '' }}">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.tasks.index') }}"
                           class="flex h-4 w-4 shrink-0 items-center justify-center rounded border border-stone-300 hover:border-primary-500 transition"
                           title="Ir a tareas"></a>
                        <div>
                            <p class="text-sm font-medium {{ $task->isOverdue() ? 'text-red-700' : 'text-stone-700' }}">
                                {{ $task->name }}
                            </p>
                            <p class="text-xs {{ $task->isOverdue() ? 'text-red-400' : 'text-stone-400' }}">
                                {{ $task->due_date->translatedFormat('d M Y') }}
                                @if ($task->isOverdue())
                                    — <span class="font-semibold">Vencida</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if ($task->isRecurring())
                        <span class="rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-700">
                            Recurrente
                        </span>
                    @endif
                </div>
            @empty
                <p class="py-4 text-center text-sm text-stone-400">Sin tareas pendientes.</p>
            @endforelse
            <div class="mt-3 pt-3 border-t border-stone-100">
                <a href="{{ route('admin.tasks.index') }}"
                   class="text-xs font-medium text-primary-600 hover:text-primary-800 transition">
                    Ver todas las tareas →
                </a>
            </div>
        </div>

        {{-- Próximas tareas --}}
        <div class="rounded-xl bg-white p-5 shadow">
            <h3 class="mb-4 text-sm font-semibold text-stone-600 uppercase">Próximas tareas</h3>
            @forelse ($upcomingTasks as $task)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-stone-100' : '' }}">
                    <div>
                        <p class="text-sm font-medium text-stone-700">{{ $task->name }}</p>
                        <p class="text-xs text-stone-400">{{ $task->due_date->translatedFormat('d M Y') }}</p>
                    </div>
                    @if ($task->isRecurring())
                        <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-500">
                            {{ match($task->repeat_type) {
                                'daily'   => 'Diaria',
                                'weekly'  => 'Semanal',
                                'monthly' => 'Mensual',
                                default   => ucfirst($task->repeat_type),
                            } }}
                        </span>
                    @endif
                </div>
            @empty
                <p class="py-4 text-center text-sm text-stone-400">Sin tareas próximas.</p>
            @endforelse
            <div class="mt-3 pt-3 border-t border-stone-100">
                <a href="{{ route('admin.tasks.index') }}"
                   class="text-xs font-medium text-primary-600 hover:text-primary-800 transition">
                    Ver todas las tareas →
                </a>
            </div>
        </div>
    </div>
</section>
