<?php 

namespace App\Actions\Transaction;
use App\Models\Transaction;

class GetTransaction
{

    /**
     * Mengambil semua transaksi dengan pagination.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllTransactions()
    {
        return Transaction::query()->orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * Mengambil detail transaksi berdasarkan ID.
     * @param int $transactionId
     * @return Transaction|null
     */
    public function getTransactionById(int $transactionId): ?Transaction
    {
        return Transaction::with('items.product')->findOrFail($transactionId);
    }

    /**
     * Mengambil detail transaksi berdasarkan reference id.
     * @param string $transactionId
     * @return Transaction|null
     */
    public function getTransactionByReferenceId(string $reference_id): ?Transaction
    {
        return Transaction::with(['items.product', 'user'])->where('reference_id', $reference_id)->first();
    }
}