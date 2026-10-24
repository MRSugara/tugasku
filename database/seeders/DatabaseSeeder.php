<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Akun contoh + kategori + beberapa tugas, untuk demo. */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Pengguna Demo',
            'email' => 'demo@tugasku.test',
            // kata sandi: password
        ]);

        $kampus = $user->categories()->create(['name' => 'Kampus']);
        $rumah = $user->categories()->create(['name' => 'Rumah']);

        $tasks = [
            ['Siapkan materi presentasi', $kampus, TaskStatus::InProgress, TaskPriority::High, 1],
            ['Kumpulkan laporan praktikum', $kampus, TaskStatus::Todo, TaskPriority::High, -1],
            ['Rapat koordinasi tim', $kampus, TaskStatus::Todo, TaskPriority::Medium, 3],
            ['Bayar tagihan listrik', $rumah, TaskStatus::Todo, TaskPriority::Medium, 0],
            ['Servis motor', $rumah, TaskStatus::Done, TaskPriority::Low, -2],
        ];

        foreach ($tasks as [$title, $category, $status, $priority, $days]) {
            $user->tasks()->create([
                'title' => $title,
                'category_id' => $category->id,
                'status' => $status,
                'priority' => $priority,
                'due_date' => today()->addDays($days),
            ]);
        }
    }
}
