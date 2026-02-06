<div class="container mx-auto p-4">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Task Manager</h1>

    
    <div class="flex flex-wrap gap-4">
        <div class="flex-[2_1_300px] bg-blue-100 p-4 rounded-lg">
            
            <!-- Filters -->
            <div class="mb-4 flex gap-4">
                <label class="inline-flex items-center">
                    <input type="text" class="rounded border-gray-300 
         focus:ring-blue-500" placeholder="Search task..." wire:model.live.debounce.300ms="search">
                </label>
                <label class="inline-flex items-center">
                    <span class="mr-2">Status</span>
                    <select wire:model.change="statusFilter" class="rounded border-gray-300 
         focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </label>
                <label class="inline-flex items-center">
                    <input class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 
           rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 
           focus:ring-offset-2 transition duration-200" type="button" wire:click="resetFilters" value="Reset"/></label>
            </div>

        

            <!-- Tasks Table -->
    <div class="bg-white p-4 sm:p-6 rounded shadow overflow-x-auto">
        <table class="w-full min-w-[600px] text-left border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 font-medium border-b">Title</th>
                    <th class="px-4 py-2 font-medium border-b">Category</th>
                    <th class="px-4 py-2 font-medium border-b">Status</th>
                    <th class="px-4 py-2 font-medium border-b">Priority</th>
                    <th class="px-4 py-2 font-medium border-b">Due Date</th>
                    <th class="px-4 py-2 font-medium border-b">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b">
                            <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-500 hover:underline">
                                {{ $task->title }}
                            </a>
                        </td>
                        <td class="px-4 py-2 border-b">{{ $task->category?->name }}</td>
                        <td class="px-4 py-2 border-b capitalize">{{ $task->status }}</td>
                        <td class="px-4 py-2 border-b capitalize">{{ $task->priority }}</td>
                        <td class="px-4 py-2 border-b">{{ $task->due_date?->format("d/m/Y") }}</td>
                        <td class="px-4 py-2 border-b space-x-2 min-w-[150px]">
                            <button 
                                wire:click="edit({{ $task->id }})"
                                class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-sm"
                            >
                                Edit
                            </button>
                            <button 
                                wire:click="delete({{ $task->id }})"
                                class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    </div>
        </div>

        <!-- Task Form -->
        <div class="flex-[1_1_150px] bg-green-100 p-4 rounded-lg max-h-[600px]">
            <form wire:submit="save">
                <input type="hidden" wire:model="taskId"/>
                <!-- Title -->
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Title</label>
                    <input 
                        type="text" 
                        wire:model="title" 
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('title')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Category</label>
                    <select 
                        wire:model="category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Description</label>
                    <textarea 
                        wire:model="description" 
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            
                <!-- Status & Priority -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block font-medium text-gray-700 mb-1">Status</label>
                        <select 
                            wire:model="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        @error('status')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex-1">
                        <label class="block font-medium text-gray-700 mb-1">Priority</label>
                        <select 
                            wire:model="priority" 
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        @error('priority')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Due Date</label>
                    <input 
                        type="date" 
                        wire:model="due_date" 
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('due_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 
           rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 
           focus:ring-offset-2 transition duration-200 my-4" type="submit">Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
