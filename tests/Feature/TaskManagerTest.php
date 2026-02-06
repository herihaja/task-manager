<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Livewire\Livewire;

class TaskManagerTest extends TestCase
{
    use RefreshDatabase;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function component_render_with_success(): void
    {
        $this->actingAs($this->user)
             ->get('/task-manager')
             ->assertStatus(200)
             ->assertSeeLivewire('task-manager');
    }

    /** @test */
    public function user_can_list_tasks(): void
    {
        $tasks = Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $component = Livewire::actingAs($this->user)
            ->test('task-manager');

        foreach($tasks as $task) {
            $component->assertSee($task->title);
        }
    }

    /** @test */
    public function user_can_create_task(): void
    {
        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->set('title', 'New Task')
            ->set('description', 'Task description')
            ->set('priority', 'high')
            ->set('status', 'pending')
            ->call('save');

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function status_is_required_on_task_creation(): void
    {
        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->set('title', 'New Task')
            ->set('description', 'Task description')
            ->set('priority', 'high')
            ->set('status', '')
            ->call('save')
            ->assertHasErrors("status");
    }

    /** @test */
    public function user_can_update_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->call('edit', $task->id)
            ->set('title', 'Updated Task Title')
            ->call('save');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
        ]);
    }

    /** @test */
    public function user_can_delete_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->call('delete', $task->id);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function user_sees_only_own_tasks_in_list(): void
    {
        Task::factory()->count(2)->create(['user_id' => $this->user->id]);
        Task::factory()->count(3)->create(['user_id' => User::factory()->create()->id]);
        $component = Livewire::actingAs($this->user)
            ->test('task-manager');
        $this->assertCount(2, $component->viewData('tasks'));
    }

    /** @test */
    public function user_can_see_task_details(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->call('edit', $task->id)
            ->assertSet('title', $task->title)
            ->assertSet('description', $task->description)
            ->assertSet('priority', $task->priority)
            ->assertSet('due_date', $task->due_date)
            ->assertSet('category_id', $task->category_id)
            ->assertSet('status', $task->status);
    }
}
