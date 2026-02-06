<script setup lang="ts">
interface Task {
    id: number;
    title: string;
    description?: string;
    status: string;
    category?: { name: string };
    user?: { name: string };
}
defineProps<{ task: Task }>();
</script>

<template>
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">{{ task.title }}</h1>

        <div class="mb-2">
            <strong>Category:</strong> {{ task.category?.name || "-" }}
        </div>
        <div class="mb-2"><strong>Status:</strong> {{ task.status }}</div>
        <div class="mb-2">
            <strong>Description:</strong> {{ task.description || "-" }}
        </div>
        <div class="mb-2">
            <strong>Assigned to:</strong> {{ task.user?.name || "-" }}
        </div>

        <a :href="route('task-manager')" class="text-blue-500">Back to tasks</a>
    </div>
</template>
