<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $pending = Task::where('is_completed', false)
            ->orderByRaw('due_date IS NULL, due_date ASC')
            ->orderBy('created_at')
            ->get();

        $completed = Task::where('is_completed', true)
            ->orderByDesc('completed_at')
            ->get();

        return view('admin.tasks.index', compact('pending', 'completed'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'repeat_type' => 'required|in:none,daily,weekly,monthly',
            'repeat_interval' => 'nullable|integer|min:1',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'due_date.date' => 'La fecha no es válida.',
            'repeat_type.in' => 'El tipo de repetición no es válido.',
            'repeat_interval.min' => 'El intervalo debe ser al menos 1.',
        ]);

        if ($validated['repeat_type'] === 'none') {
            $validated['repeat_interval'] = null;
            $validated['next_due_date'] = null;
        } else {
            $validated['repeat_interval'] = $validated['repeat_interval'] ?? 1;
        }

        $task = Task::create($validated);

        if ($task->isRecurring()) {
            $task->update(['next_due_date' => $task->calculateNextDueDate()]);
        }

        return redirect()->route('admin.tasks.index')->with('success', 'Tarea creada exitosamente.');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'repeat_type' => 'required|in:none,daily,weekly,monthly',
            'repeat_interval' => 'nullable|integer|min:1',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'due_date.date' => 'La fecha no es válida.',
            'repeat_type.in' => 'El tipo de repetición no es válido.',
            'repeat_interval.min' => 'El intervalo debe ser al menos 1.',
        ]);

        if ($validated['repeat_type'] === 'none') {
            $validated['repeat_interval'] = null;
            $validated['next_due_date'] = null;
        } else {
            $validated['repeat_interval'] = $validated['repeat_interval'] ?? 1;
        }

        $task->update($validated);

        if ($task->isRecurring()) {
            $task->update(['next_due_date' => $task->calculateNextDueDate()]);
        }

        return redirect()->route('admin.tasks.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function toggle(Task $task)
    {
        if (!$task->is_completed) {
            // Completing the task
            $task->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            // If recurring, create the next occurrence
            if ($task->isRecurring()) {
                Task::create([
                    'name' => $task->name,
                    'due_date' => $task->calculateNextDueDate(),
                    'repeat_type' => $task->repeat_type,
                    'repeat_interval' => $task->repeat_interval,
                    'next_due_date' => null,
                ]);

                // Calculate next_due_date for the newly created task
                $newTask = Task::where('is_completed', false)
                    ->where('name', $task->name)
                    ->latest()
                    ->first();

                if ($newTask && $newTask->isRecurring()) {
                    $newTask->update(['next_due_date' => $newTask->calculateNextDueDate()]);
                }

                return redirect()->route('admin.tasks.index')->with('success', 'Tarea completada. Se creó la siguiente ocurrencia.');
            }

            return redirect()->route('admin.tasks.index')->with('success', 'Tarea completada.');
        } else {
            // Uncompleting the task
            $task->update([
                'is_completed' => false,
                'completed_at' => null,
            ]);

            return redirect()->route('admin.tasks.index')->with('success', 'Tarea marcada como pendiente.');
        }
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')->with('success', 'Tarea eliminada exitosamente.');
    }
}
