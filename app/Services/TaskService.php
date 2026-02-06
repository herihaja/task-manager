<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
                'due_date' => $data['due_date'] ?: null,
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

    public function searchTasks(User $user, string $searchTerm='', string $status='', $perPage=20): LengthAwarePaginator
    {
        $query = Task::where('user_id', $user->id);
        if ($status) {
            $query->where('status', $status);
        }

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->with('category')->paginate($perPage);
    }
}
