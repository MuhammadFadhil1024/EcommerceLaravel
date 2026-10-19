<?php

namespace App\Actions\Discount;

use App\Models\Discount;
use App\Models\DiscountTarget;

class UpdateDiscount {
    public function updateDiscount(Discount $discount, $data, $discountCode, $selected_target = [], $target_type = 'NONE') {
        // Update discount fields with defensive defaults
        $discount->name = $data['discount_name'] ?? '';
        $discount->code = $discountCode;
        $discount->level = $data['level'] ?? '';
        $discount->value_type = $data['type'] ?? 'FIXED_AMOUNT';
        $discount->value = $data['discount_value'] ?? 0;
        $discount->max_discount_amount = $data['discount_cap'] ?? null;
        $discount->min_purchase_amount = $data['minimum_purchase'] ?? 0;
        $discount->start_at = $data['start_date'] ?? null;
        $discount->end_at = $data['end_date'] ?? null;
        $discount->usage_limit_total = $data['discount_limit'] ?? null;  // Fixed: model uses usage_limit_total
        $discount->usage_limit_per_user = $data['discount_limit_per_user'] ?? null;  // Fixed: model uses usage_limit_per_user
        $discount->is_stackable = $data['is_stacakble'] ?? 0;
        $discount->priority = $data['discount_priority'] ?? 0;
        $discount->status = $data['status'] ?? 'DRAFT';
        $discount->target_type = $target_type;
        $discount->save();

        // Handle target updates: remove existing targets and add new ones
        $discount->discountTargets()->delete();

        // If level is PRODUCT or CATEGORY and targets selected, save new targets
        if (in_array($data['level'] ?? '', ['PRODUCT', 'CATEGORY']) && !empty($selected_target)) {
            foreach ($selected_target as $targetId) {
                DiscountTarget::create([
                    'discount_id' => $discount->id,
                    'target_type' => $target_type,
                    'target_id' => $targetId,
                ]);
            }
        }

        return $discount;
    }
}