<?php

namespace App\Actions\Payment;

use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Actions\Frontend\DecreaseStock;

class HandleXenditWebhook
{
    protected array $statusMapping = [
        'COMPLETED' => 'paid',
        'EXPIRED' => 'cancelled',
        'CANCELED' => 'cancelled',
    ];

    public function handle(array $payload): void
    {
        $event = $payload['event'] ?? '';
        $data = $payload['data'] ?? [];

        if ($event === '') {
            throw new Exception('Webhook event tidak diketahui.');
        }

        if (empty($data)) {
            throw new Exception('Data webhook kosong.');
        }

        if (! str_starts_with($event, 'payment_session.')) {
            return;
        }

        $referenceId = $data['reference_id'] ?? '';
        $sessionStatus = $data['status'] ?? '';
        $paymentId = $data['payment_id'] ?? '';

        if ($referenceId === '') {
            throw new Exception('reference_id tidak ditemukan di payload webhook.');
        }

        try {
            $this->processEvent($event, $referenceId, $sessionStatus, $paymentId, $data);
        } catch (Exception $e) {
            Log::error('HandleXenditWebhook failed', [
                'event' => $event,
                'reference_id' => $referenceId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    protected function processEvent(
        string $event,
        string $referenceId,
        string $sessionStatus,
        string $paymentId,
        array $data,
    ): void {
        $newStatus = $this->statusMapping[$sessionStatus] ?? null;

        if ($newStatus === null) {
            Log::info('Xendit webhook status tidak dipetakan', [
                'event' => $event,
                'session_status' => $sessionStatus,
                'reference_id' => $referenceId,
            ]);
            return;
        }

        $transaction = Transaction::where('reference_id', $referenceId)->first();

        if (! $transaction) {
            Log::warning('Xendit webhook: transaksi tidak ditemukan', [
                'reference_id' => $referenceId,
                'event' => $event,
            ]);
            return;
        }

        if ($transaction->status === 'paid') {
            Log::info('Xendit webhook: transaksi sudah paid, skip update', [
                'reference_id' => $referenceId,
                'event' => $event,
            ]);
            return;
        }

        $updateData = [
            'status' => $newStatus,
            'session_id' => $data['payment_session_id'] ?? $transaction->session_id,
            'payment_date' => $newStatus === 'paid' ? now() : $transaction->payment_date,
        ];

        if ($paymentId !== '') {
            $updateData['payment_method'] = 'Xendit';
        }

        $transaction->update($updateData);

        // decrease stock jika status berubah menjadi paid
        if ($newStatus === 'paid') {
            app(DecreaseStock::class)->handleDecreaseStock($transaction);
        }

        Log::info('Xendit webhook: transaksi berhasil diupdate', [
            'reference_id' => $referenceId,
            'old_status' => $transaction->getOriginal('status'),
            'new_status' => $newStatus,
            'payment_id' => $paymentId,
        ]);
    }
}
