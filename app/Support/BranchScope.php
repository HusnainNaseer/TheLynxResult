<?php

namespace App\Support;

use App\Models\User;

class BranchScope
{
    public static function coordinatorBranchId(?User $user = null): ?string
    {
        $user ??= auth()->user();

        if (!$user || !$user->hasRole('Coordinator') || !$user->branch_id) {
            return null;
        }

        return (string) $user->branch_id;
    }

    public static function apply($query, string $column = 'erp_branch_id')
    {
        $branchId = self::coordinatorBranchId();

        return $branchId ? $query->where($column, $branchId) : $query;
    }

    public static function abortIfCoordinatorOutside($branchId): void
    {
        $coordinatorBranchId = self::coordinatorBranchId();

        if ($coordinatorBranchId && (string) $branchId !== $coordinatorBranchId) {
            abort(403, 'You can only access records for your own branch.');
        }
    }
}
