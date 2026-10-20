<?php

namespace App\Actions\Discount;

use App\Models\DiscountAuditLog;

class GetDiscountLog
{
    public function getByIdWithRelations(int $logId): ?DiscountAuditLog
    {
        return DiscountAuditLog::with(['discount', 'admin'])->find($logId);
    }
}
