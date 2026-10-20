<flux:modal name="discount-log-changes" class="md:w-[720px]">
    <div class="space-y-6">
        @php
            $changes = $changes ?? [];
            $oldData = $oldData ?? null;
            $newData = $newData ?? null;
        @endphp

        @if ($log)
            <div>
                <flux:heading size="lg">
                    {{ ucfirst(strtolower($log->action)) }} Discount
                </flux:heading>
                <flux:text class="mt-2">
                    {{ $log->discount?->name ?? 'Discount #' . $log->discount_id }}
                    · {{ $log->admin?->name ?? 'Unknown' }}
                    · {{ $log->created_at?->format('d M Y, H:i') }}
                </flux:text>
            </div>

            <div>
                <flux:heading size="sm" class="mb-2">
                    Changes <span class="text-zinc-400">({{ count($changes) }})</span>
                </flux:heading>

                @if (! empty($changes))
                    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div
                            class="grid grid-cols-[1.2fr_1fr_1fr] gap-2 border-b border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800">
                            <div>Field</div>
                            <div>Old</div>
                            <div>New</div>
                        </div>

                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($changes as $change)
                                <div class="grid grid-cols-[1.2fr_1fr_1fr] gap-2 px-3 py-2 text-sm">
                                    <div class="font-medium text-zinc-800 dark:text-zinc-100">{{ $change['label'] }}</div>
                                    <div class="truncate text-zinc-400" title="{{ $change['old'] }}">{{ $change['old'] }}
                                    </div>
                                    <div class="truncate text-green-700 dark:text-green-400" title="{{ $change['new'] }}">
                                        {{ $change['new'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <flux:text class="text-sm text-zinc-500">No field changes.</flux:text>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <flux:heading size="sm" class="mb-2">Old Data</flux:heading>

                    @if (! empty($oldData))
                        <pre class="max-h-80 overflow-auto rounded-lg bg-zinc-50 p-3 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">{{ json_encode($oldData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    @else
                        <flux:text class="text-sm text-zinc-500">No previous data.</flux:text>
                    @endif
                </div>

                <div>
                    <flux:heading size="sm" class="mb-2">New Data</flux:heading>

                    @if (! empty($newData))
                        <pre class="max-h-80 overflow-auto rounded-lg bg-zinc-50 p-3 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">{{ json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    @else
                        <flux:text class="text-sm text-zinc-500">No new data.</flux:text>
                    @endif
                </div>
            </div>

            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Close</flux:button>
                </flux:modal.close>
            </div>
        @endif
    </div>
</flux:modal>
