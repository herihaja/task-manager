<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_list_tasks(): void
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
             ->get('/api/tasks')
             ->assertStatus(200)
             ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_create_task(): void
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'priority' => 'high',
            'due_date' => now()->addWeek()->toDateString(),
            'status' => 'pending',
        ];

        $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData)
            ->assertStatus(201)
            ->assertJsonFragment(['title' => 'New Task']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function status_is_required_on_task_creation(): void
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'priority' => 'high',
            'due_date' => now()->addWeek()->toDateString(),
        ];

        $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData)
            ->assertJsonFragment(['status' => ['The status field is required.']]);
    }

    /** @test */
    public function user_can_update_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'title' => 'Updated Task',
            'status' => 'in_progress',
        ];

        $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", $updateData)
            ->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Task', 'status' => 'in_progress']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function user_can_delete_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_cannot_access_others_tasks(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($this->user)
            ->getJson("/api/tasks/{$task->id}")
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", ['title' => 'Hacked'])
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_view_task_details(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->getJson("/api/tasks/{$task->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['title' => $task->title]);
    }

    /** @test */
    public function return_404_when_task_not_found(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/tasks/99999')
            ->assertStatus(404);
    }

    /** @test */
    public function anonymous_user_cannot_access_tasks(): void
    {
        $this->getJson('/api/tasks')
            ->assertStatus(401);

        $this->postJson('/api/tasks', [])
            ->assertStatus(401);

        $this->putJson('/api/tasks/1', [])
            ->assertStatus(401);

        $this->deleteJson('/api/tasks/1')
            ->assertStatus(401);
    }

    /** @test */
    public function user_sees_only_own_tasks_in_list(): void
    {
        Task::factory()->count(2)->create(['user_id' => $this->user->id]);
        Task::factory()->count(3)->create(['user_id' => User::factory()->create()->id]);

        $this->actingAs($this->user)
             ->get('/api/tasks')
             ->assertStatus(200)
             ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function api_can_search_tasks()
    {
        $user = User::factory()->create();

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Do laundry',
            'status' => 'pending'
        ]);
        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Wash car',
            'status' => 'completed'
        ]);

        $this->actingAs($user)
            ->getJson('/api/tasks?search=laundry&status=pending')
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Do laundry']);
    }

    /** @test */
    public function user_should_not_be_able_to_use_category_he_does_not_own()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Category owned by another user
        $otherCategory = Category::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $payload = [
            'title' => 'Test task',
            'priority' => 'low',
            'status' => 'pending',
            'category_id' => $otherCategory->id,
            'due_date' => now()->addWeek()->toDateString(),
        ];

        $this->actingAs($user)
            ->postJson('/api/tasks', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }
}
