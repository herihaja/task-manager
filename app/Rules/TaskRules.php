<?php

namespace App\Rules;

class TaskRules
{
    public static function rules(bool $isUpdate=false): array
    {

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
        ];

        if ($isUpdate) {
            $rules['title'] = 'sometimes|' . $rules['title'];
            $rules['priority'] = 'sometimes|' . $rules['priority'];
            $rules['status'] = 'sometimes|' . $rules['status'];
        }

        return $rules;
    }

    public static function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'priority.required' => 'The priority field is required.',
            'priority.in' => 'The selected priority is invalid.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
            'due_date.date' => 'The due date is not a valid date.',
            'category_id.exists' => 'The selected category does not exist.',
        ];
    }
}
