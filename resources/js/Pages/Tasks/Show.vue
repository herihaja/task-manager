<script setup lang="ts">
// Task interface
interface Task {
    id: number;
    title: string;
    description?: string;
    status: string;
    category?: { name: string } | null;
    user?: { name: string } | null;
    priority?: string;
}

defineProps<{ task: Task }>();
</script>

<script lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout, // assign layout here
    props: {
        task: Object,
    },
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Title -->
        <h1
            class="text-3xl sm:text-4xl font-bold text-gray-800 mb-6 text-center sm:text-left"
        >
            {{ task.title }}
        </h1>

        <!-- Task Details Card -->
        <div class="bg-white shadow-md rounded-lg p-6 sm:p-8 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="font-semibold text-gray-700">Category:</span>
                    <span class="text-gray-900">{{
                        task.category?.name || "-"
                    }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Status:</span>
                    <span
                        :class="{
                            'text-green-600': task.status === 'completed',
                            'text-yellow-500': task.status === 'in-progress',
                            'text-red-500': task.status === 'pending',
                        }"
                    >
                        {{ task.status }}
                    </span>
                </div>
                <div>
                    <span class="font-semibold text-gray-700"
                        >Assigned to:</span
                    >
                    <span class="text-gray-900">{{
                        task.user?.name || "-"
                    }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Priority:</span>
                    <span
                        :class="{
                            'text-red-600': task.priority === 'high',
                            'text-yellow-500': task.priority === 'medium',
                            'text-green-600': task.priority === 'low',
                        }"
                    >
                        {{ task.priority || "-" }}
                    </span>
                </div>
            </div>

            <!-- Description -->
            <div>
                <span class="font-semibold text-gray-700">Description:</span>
                <p class="text-gray-800 mt-1">{{ task.description || "-" }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between mt-6 gap-2">
                <a
                    :href="route('task-manager')"
                    class="text-white bg-blue-600 hover:bg-blue-700 rounded-md px-4 py-2 text-center sm:text-left"
                >
                    Back to Tasks
                </a>
            </div>
        </div>
    </div>
</template>
