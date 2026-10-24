<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    /** Daftar tugas milik pengguna, bisa dicari dan difilter. */
    public function index(Request $request): View
    {
        $tasks = $request->user()->tasks()
            ->with('category')
            ->filter($request->only('search', 'status', 'category'))
            ->orderByRaw("case when status = 'done' then 1 else 0 end") // yang selesai di bawah
            ->orderBy('due_date')
            ->paginate(10)
            ->withQueryString();

        return view('tasks.index', [
            'tasks' => $tasks,
            'categories' => $request->user()->categories()->orderBy('name')->get(),
            'statuses' => TaskStatus::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('tasks.create', $this->formData($request));
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $request->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')->with('status', 'Tugas berhasil ditambahkan.');
    }

    public function edit(Request $request, Task $task): View
    {
        Gate::authorize('update', $task);

        return view('tasks.edit', ['task' => $task] + $this->formData($request));
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $task->update($request->validated());

        return redirect()->route('tasks.index')->with('status', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('status', 'Tugas dihapus.');
    }

    /**
     * Dipanggil lewat AJAX (fetch) dari tombol "Lanjut" di kartu tugas.
     * Memindahkan status satu langkah: belum → sedang → selesai.
     */
    public function updateStatus(Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $next = $task->status->next();

        if ($next) {
            $task->update(['status' => $next]);
        }

        return response()->json([
            'status' => $task->status->value,
            'label' => $task->status->label(),
            'color' => $task->status->color(),
            'done' => $task->status === TaskStatus::Done,
        ]);
    }

    /** Data yang dibutuhkan form tambah & ubah. */
    private function formData(Request $request): array
    {
        return [
            'categories' => $request->user()->categories()->orderBy('name')->get(),
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ];
    }
}
