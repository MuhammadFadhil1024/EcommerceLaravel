<?php

namespace App\Actions\Discount;

use App\Models\DiscountAuditLog;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchDiscountLogs
{
    public function execute(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? '';
        $action = $filters['action'] ?? '';

        return DiscountAuditLog::query()
            ->with(['discount', 'admin'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('discount', function ($discount) use ($search) {
                        $discount->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })->orWhereHas('admin', function ($admin) use ($search) {
                        $admin->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when($action !== '', fn ($query) => $query->where('action', $action))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
