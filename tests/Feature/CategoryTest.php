<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_category(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/categories', ['name' => 'Kampus'])->assertRedirect();

        $this->assertSame(['Kampus'], $user->categories()->pluck('name')->all());
    }

    public function test_category_name_is_unique_per_user(): void
    {
        $user = User::factory()->create();
        Category::factory()->for($user)->create(['name' => 'Kampus']);

        $this->actingAs($user)->post('/categories', ['name' => 'Kampus'])->assertSessionHasErrors('name');

        // Pengguna lain boleh memakai nama yang sama.
        $this->actingAs(User::factory()->create())->post('/categories', ['name' => 'Kampus'])->assertSessionHasNoErrors();
    }

    public function test_deleting_a_category_keeps_its_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();
        $task = Task::factory()->for($user)->create(['category_id' => $category->id]);

        $this->actingAs($user)->delete("/categories/{$category->id}")->assertRedirect();

        $this->assertNull($category->fresh());
        $this->assertNull($task->fresh()->category_id);
    }

    public function test_cannot_delete_another_users_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->create())->delete("/categories/{$category->id}")->assertForbidden();
        $this->assertNotNull($category->fresh());
    }
}
