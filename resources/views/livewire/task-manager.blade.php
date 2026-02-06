<div>
    <h1>Task Manager</h1>

    <!-- Filters -->
    <div>
        <label><input type="text" placeholder="Search task..." wire:model.live.debounce.300ms="search"></label>
        <label>
            <span>Status</span>
            <select wire:model.change="statusFilter">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </label>
    </div>

    <!-- Task Form -->
    <div>
        <form wire:submit="save">
            <input type="hidden" wire:model="taskId"/>
            <label>
                <span>Title</span>
                <input wire:model="title"/>
                @error('title')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </label>

            <label>
                <span>Category</span>
                <select wire:model="category_id">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }} </option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </label>

            <label>
                <span>description</span>
                <textarea wire:model="description"></textarea>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </label>

            <label>
                <span>Status</span>
                <select wire:model="status">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
                @error('status')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </label>

            <label>
                <span>Priority</span>
                <select wire:model="priority">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                @error('priority')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </label>

            <label>
                <span>Due Date</span>
                <input type="date" wire:model="due_date"/>
                @error('due_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </label>

            <button type="submit">Save Task</button>
        </form>
    </div>

    <!-- Tasks list -->
    <div>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>
                        <a href="{{ route('tasks.show', $task->id) }}">
                            {{ $task->title }}
                        </a>
                    </td>
                    <td>{{ $task->category?->name }}</td>
                    <td>{{ $task->status }}</td>
                    <td>{{ $task->priority }}</td>
                    <td>{{ $task->due_date }}</td>
                    <td>
                        <button wire:click="edit({{ $task->id }})">Edit</button>
                        <button wire:click="delete({{ $task->id }})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
