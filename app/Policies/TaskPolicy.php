<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

/**
 * Aturan izin tugas: hanya pemiliknya yang boleh mengubah atau menghapus.
 */
class TaskPolicy
{
    public function update(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }
}
