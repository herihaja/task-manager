<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Services\TaskService;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;
    private TaskService $taskService;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->taskService = app(TaskService::class);
        $this->user = User::factory()->create();
    }

    /* @test */
    public function test_create_task(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
            'priority' => 'medium',
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending',
        ];

        $task = $this->taskService->createTask($taskData, $this->user);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    /* @test */
    public function test_update_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated description.',
            'priority' => 'high',
            'due_date' => now()->addDays(10)->toDateString(),
            'status' => 'in_progress',
        ];

        $task = $this->taskService->updateTask($task, $updateData);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'priority' => 'high',
        ]);
    }

    /* @test */
    public function test_delete_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $this->taskService->deleteTask($task);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /* @test */
    public function test_should_search_tasks(): void
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'First Task',
            'status' => 'pending',
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Second Task',
            'status' => 'completed',
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Third Task',
            'status' => 'in_progress',
            'description' => 'This is the third task description.',
        ]);

        $results = $this->taskService->searchTasks($this->user, 'First', '');
        $this->assertCount(1, $results->items());
        $this->assertEquals('First Task', $results->items()[0]->title);

        $results = $this->taskService->searchTasks($this->user, '', 'completed');
        $this->assertCount(1, $results->items());
        $this->assertEquals('Second Task', $results->items()[0]->title);

        $results = $this->taskService->searchTasks($this->user, 'description.');
        $this->assertCount(1, $results->items());
        $this->assertEquals('Third Task', $results->items()[0]->title);
    }

    /* @test */
    public function test_should_search_tasks_with_pagination(): void
    {
        Task::factory()->count(50)->create([
            'user_id' => $this->user->id,
        ]);

        $results = $this->taskService->searchTasks($this->user);
        $this->assertCount(20, $results->items());
        $this->assertEquals(50, $results->total());
        $this->assertEquals(3, $results->lastPage());
        $this->assertEquals(1, $results->currentPage());
        $this->assertEquals(20, $results->perPage());
        $this->assertStringContainsString("page=2", $results->nextPageUrl());
        $this->assertEquals("", $results->previousPageUrl());
    }

    /* @test */
    public function test_should_return_none_if_no_tasks_match_search(): void
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Laravel developer technical testing',
        ]);

        $results = $this->taskService->searchTasks($this->user, 'Nonexistent');
        $this->assertCount(0, $results->items());
    }
}
