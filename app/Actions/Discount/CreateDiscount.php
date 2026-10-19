<?php 

namespace App\Actions\Discount;

use Illuminate\Support\Facades\Auth;

class CreateDiscount {
    public function createDiscount($data, $discountCode, $selected_target = [], $target_type = 'NONE') {
        // Create the discount
        $discount = \App\Models\Discount::create([
            'name' => $data['discount_name'],
            'code' => $discountCode,
            'level' => $data['level'],
            'value_type' => $data['type'],
            'value' => $data['discount_value'],
            'max_discount_amount' => $data['discount_cap'] ?? null,
            'min_purchase_amount' => $data['minimum_purchase'] ?? 0,
            'start_at' => $data['start_date'] ?? null,
            'end_at' => $data['end_date'] ?? null,
            'usage_limit_total' => $data['discount_limit'] ?? null,
            'usage_limit_per_user' => $data['discount_limit_per_user'] ?? null,
            'is_stackable' => $data['is_stacakble'],
            'priority' => $data['discount_priority'] ?? 0,
            'status' => $data['status'],
            'created_by' => Auth::user()->id,
        ]);

        // Save targets if level is PRODUCT or CATEGORY (not TRANSACTION) and targets selected
        if (in_array($data['level'], ['PRODUCT', 'CATEGORY']) && !empty($selected_target)) {
            foreach ($selected_target as $targetId) {
                \App\Models\DiscountTarget::create([
                    'discount_id' => $discount->id,
                    'target_type' => $target_type,
                    'target_id' => $targetId,
                ]);
            }
        }

        // If TRANSACTION level, set target_type to NONE on the discount
        if ($data['level'] === 'TRANSACTION') {
            $discount->update(['target_type' => 'NONE']);
        }

        return $discount;
    }
}