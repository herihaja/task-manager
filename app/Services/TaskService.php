<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Collection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{

    public function createTask(array $data, User $user): Task
    {
        return DB::transaction(function () use ($data, $user) {
            return Task::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'priority' => $data['priority'] ?? 'medium',
                'due_date' => $data['due_date'] ?? null,
            ]);
        });
    }

    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
    }

    public function markTaskAsCompleted(Task $task): Task
    {
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $task;
    }

    public function markTaskAsInProgress(Task $task): Task
    {
        $task->update([
            'status' => 'in_progress',
            'completed_at' => null,
        ]);

        return $task;
    }

    public function getTasksByStatus(User $user, string $status): Collection
    {
        return Task::where('user_id', $user->id)
            ->where('status', $status)
            ->with('category')
            ->get();
    }

    public function getTasksByCategory(User $user, int $categoryId): Collection
    {
        return Task::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->with('category')
            ->get();
    }

    public function getTasksForUser(int $userId): Collection
    {
        return Task::where('user_id', $userId)->with('category')->get();
    }
}
