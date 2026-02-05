<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function createCategory(array $data, User $user): Category
    {
        return DB::transaction(function () use ($data, $user) {
            return Category::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'color' => $data['color'] ?? null,
            ]);
        });
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }

    public function getCategoriesByUser(User $user): Collection
    {
        return Category::where('user_id', $user->id)->get();
    }

    public function taskCountByCategory(Category $category): int
    {
        return $category->tasks()->count();
    }

    public function getCategoryById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function categoryBelongsToUser(Category $category, User $user): bool
    {
        return $category->user_id === $user->id;
    }
}
