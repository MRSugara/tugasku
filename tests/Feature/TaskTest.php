<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_login(): void
    {
        $this->get('/tasks')->assertRedirect('/login');
    }

    public function test_user_can_create_a_task(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();

        $this->actingAs($user)->post('/tasks', [
            'title' => 'Kerjakan laporan',
            'category_id' => $category->id,
            'status' => 'todo',
            'priority' => 'high',
            'due_date' => '2026-12-01',
        ])->assertRedirect('/tasks');

        $task = Task::first();
        $this->assertSame('Kerjakan laporan', $task->title);
        $this->assertSame($user->id, $task->user_id);
        $this->assertSame($category->id, $task->category_id);
    }

    public function test_title_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', ['title' => '', 'status' => 'todo', 'priority' => 'low'])
            ->assertSessionHasErrors('title');

        $this->assertSame(0, Task::count());
    }

    public function test_cannot_use_another_users_category(): void
    {
        $user = User::factory()->create();
        $otherCategory = Category::factory()->create();

        $this->actingAs($user)->post('/tasks', [
            'title' => 'Tugas', 'status' => 'todo', 'priority' => 'low', 'category_id' => $otherCategory->id,
        ])->assertSessionHasErrors('category_id');
    }

    public function test_list_only_shows_own_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->create(['title' => 'Tugas saya']);
        Task::factory()->create(['title' => 'Tugas orang lain']);

        $this->actingAs($user)->get('/tasks')
            ->assertOk()
            ->assertSee('Tugas saya')
            ->assertDontSee('Tugas orang lain');
    }

    public function test_list_can_be_filtered_by_status_and_search(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->create(['title' => 'Belajar Laravel', 'status' => TaskStatus::Todo]);
        Task::factory()->for($user)->create(['title' => 'Belajar Tailwind', 'status' => TaskStatus::Done]);
        Task::factory()->for($user)->create(['title' => 'Belanja bulanan', 'status' => TaskStatus::Todo]);

        $this->actingAs($user)->get('/tasks?search=Belajar&status=todo')
            ->assertSee('Belajar Laravel')
            ->assertDontSee('Belajar Tailwind')
            ->assertDontSee('Belanja bulanan');
    }

    public function test_user_can_update_and_delete_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->actingAs($user)->put("/tasks/{$task->id}", [
            'title' => 'Judul baru', 'status' => 'in_progress', 'priority' => 'medium',
        ])->assertRedirect('/tasks');
        $this->assertSame('Judul baru', $task->fresh()->title);

        $this->actingAs($user)->delete("/tasks/{$task->id}")->assertRedirect('/tasks');
        $this->assertNull($task->fresh());
    }

    public function test_cannot_touch_another_users_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $this->actingAs($user)->get("/tasks/{$task->id}/edit")->assertForbidden();
        $this->actingAs($user)->delete("/tasks/{$task->id}")->assertForbidden();
        $this->actingAs($user)->patchJson("/tasks/{$task->id}/status")->assertForbidden();
        $this->assertNotNull($task->fresh());
    }

    public function test_status_moves_one_step_via_ajax(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['status' => TaskStatus::Todo]);

        $this->actingAs($user)->patchJson("/tasks/{$task->id}/status")
            ->assertOk()
            ->assertJson(['status' => 'in_progress', 'label' => 'Sedang dikerjakan', 'done' => false]);

        $this->actingAs($user)->patchJson("/tasks/{$task->id}/status")
            ->assertJson(['status' => 'done', 'done' => true]);

        // Sudah selesai: tidak berubah lagi.
        $this->actingAs($user)->patchJson("/tasks/{$task->id}/status")->assertJson(['status' => 'done']);
    }

    public function test_dashboard_counts_tasks_per_status(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->count(2)->create(['status' => TaskStatus::Todo]);
        Task::factory()->for($user)->create(['status' => TaskStatus::Done]);

        $response = $this->actingAs($user)->get('/dashboard')->assertOk();

        $this->assertSame(2, $response->viewData('counts')['todo']);
        $this->assertSame(1, $response->viewData('counts')['done']);
    }
}
