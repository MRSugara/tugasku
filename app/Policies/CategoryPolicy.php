<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

/**
 * Aturan izin kategori: hanya pemiliknya yang boleh menghapus.
 */
class CategoryPolicy
{
    public function delete(User $user, Category $category): bool
    {
        return $category->user_id === $user->id;
    }
}
