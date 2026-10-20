<?php

namespace App\Actions\Discount;

use Illuminate\Support\Str;

class CompareDiscountChanges
{
    public function compare(?array $oldData, ?array $newData): array
    {
        $oldData = $oldData ?? [];
        $newData = $newData ?? [];

        $order = array_merge(RecordDiscountAudit::FIELDS, ['targets']);
        $keys = array_values(array_unique(array_merge($order, array_keys($oldData), array_keys($newData))));

        $changes = [];

        foreach ($keys as $field) {
            if (! array_key_exists($field, $oldData) && ! array_key_exists($field, $newData)) {
                continue;
            }

            $old = $oldData[$field] ?? null;
            $new = $newData[$field] ?? null;

            if (is_array($old) && $old === []) {
                $old = null;
            }

            if (is_array($new) && $new === []) {
                $new = null;
            }

            if ($old == $new) {
                continue;
            }

            $changes[] = [
                'field' => $field,
                'label' => Str::headline($field),
                'old' => $this->format($old),
                'new' => $this->format($new),
            ];
        }

        return $changes;
    }

    private function format($value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return empty($value) ? '—' : implode(', ', array_map(fn ($item) => (string) $item, $value));
        }

        return (string) $value;
    }
}
