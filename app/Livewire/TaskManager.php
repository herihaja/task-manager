<?php

namespace App\Livewire;

use App\Models\Task;
use App\Rules\TaskRules;
use Livewire\Component;
use App\Services\TaskService;
use App\Services\CategoryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\WithPagination;

class TaskManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public $categories;
    public $title, $description, $priority, $due_date, $category_id, $taskId, $status;
    public $search = '';
    public $statusFilter = '';
    public $categoryId;
    private $taskService;
    private $categoryService;

    protected function messages()
    {
        return TaskRules::messages();
    }

    public function boot(TaskService $taskService, CategoryService $categoryService)
    {
        $this->taskService = $taskService;
        $this->categoryService = $categoryService;
    }

    public function mount()
    {
        $this->categories = $this->categoryService->getCategoriesByUser(auth()->user());
        $this->resetInputFields();
    }

    public function save()
    {
        $rules = $this->taskId ? TaskRules::rules(true) : TaskRules::rules();
        $data = $this->validate($rules, $this->messages());

        if ($this->taskId) {
            $task = Task::findOrFail($this->taskId);
            $this->authorize('update', $task);

            $this->taskService->updateTask($task, $data);
        } else {
            $this->taskService->createTask($data, auth()->user());
        }

        $this->resetInputFields();
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
        $this->authorize('view', $task);

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
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);
        $this->resetInputFields();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.task-manager', [
            'tasks' => $this->taskService->searchTasks(
                auth()->user(),
                $this->search,
                $this->statusFilter
            )
        ]);
    }
}
