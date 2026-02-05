<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use App\Services\TaskService;
use App\Services\CategoryService;

class TaskManager extends Component
{
    public $tasks;
    public $categories;
    public $title, $description, $priority, $due_date, $category_id, $taskId, $status;
    public $search = '';
    public $statusFilter = '';
    public $categoryId;
    private $taskService;
    private $categoryService;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }

    public function boot(TaskService $taskService, CategoryService $categoryService)
    {
        $this->taskService = $taskService;
        $this->categoryService = $categoryService;
    }

    public function mount()
    {
        $this->tasks = $this->taskService->getTasksForUser(auth()->user()->id);
        $this->categories = $this->categoryService->getCategoriesByUser(auth()->user());
        $this->resetInputFields();
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->taskId) {
            $task = Task::findOrFail($this->taskId);
            if ($task->user_id !== auth()->id()) abort(403);

            $this->taskService->updateTask($task, $data);
        } else {
            $this->taskService->createTask($data, auth()->user());
        }

        $this->resetInputFields();
        $this->refreshTasks();
    }

    public function resetInputFields()
    {
        $this->title = '';
        $this->description = '';
        $this->priority = 'medium';
        $this->due_date = '';
        $this->category_id = null;
        $this->taskId = null;
        $this->status = 'pending';
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        if ($task->user_id !== auth()->id()) abort(403);

        $this->title = $task->title;
        $this->description = $task->description;
        $this->priority = $task->priority;
        $this->due_date = $task->due_date;
        $this->category_id = $task->category_id;
        $this->status = $task->status;
        $this->taskId = $id;
    }

    public function delete($id)
    {
        $task = Task::findOrFail($id);
        if ($task->user_id !== auth()->id()) abort(403);

        $this->taskService->deleteTask($task);
        $this->refreshTasks();
        $this->resetInputFields();
    }

    public function updatedStatusFilter($value)
    {
        $this->refreshTasks();
    }

    public function updatedSearch()
    {
        $this->refreshTasks();
    }

    public function render()
    {
        return view('livewire.task-manager');
    }

    private function refreshTasks()
    {
        $this->tasks = $this->taskService->searchTasks(auth()->user(), $this->search, $this->statusFilter);
    }
}
