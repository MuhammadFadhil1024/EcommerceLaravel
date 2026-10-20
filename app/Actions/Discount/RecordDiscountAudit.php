<?php

namespace App\Actions\Discount;

use App\Models\Discount;
use App\Models\DiscountAuditLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class RecordDiscountAudit
{
    public const FIELDS = [
        'name',
        'description',
        'code',
        'level',
        'target_type',
        'value_type',
        'value',
        'max_discount_amount',
        'min_purchase_amount',
        'start_at',
        'end_at',
        'usage_limit_total',
        'usage_limit_per_user',
        'is_stackable',
        'priority',
        'status',
    ];

    public function snapshot(Discount $discount): array
    {
        $data = Arr::only($discount->toArray(), self::FIELDS);

        $data['targets'] = $discount->discountTargets()
            ->orderBy('target_id')
            ->pluck('target_id')
            ->all();

        return $data;
    }

    public function record(int $discountId, string $action, ?array $oldData, ?array $newData): void
    {
        $adminId = Auth::id();

        if (! $adminId) {
            return;
        }

        DiscountAuditLog::create([
            'discount_id' => $discountId,
            'admin_id' => $adminId,
            'action' => $action,
            'old_data' => $oldData,
            'new_data' => $newData,
            'created_at' => now(),
        ]);
    }

    public function created(Discount $discount): void
    {
        $discount->refresh();

        $this->record($discount->id, 'CREATE', null, $this->snapshot($discount));
    }

    public function updated(Discount $discount, array $oldSnapshot): void
    {
        $discount->refresh();

        $newSnapshot = $this->snapshot($discount);

        if ($oldSnapshot == $newSnapshot) {
            return;
        }

        $this->record($discount->id, 'UPDATE', $oldSnapshot, $newSnapshot);
    }

    public function deleted(Discount $discount, array $oldSnapshot): void
    {
        $this->record($discount->id, 'DELETE', $oldSnapshot, null);
    }
}
