<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    /**
     * Данная функция вносит средства на кошелек пользователя.
     *
     * @param int $userId идентификатор пользователя
     * @param float $amount сумма депозита
     * @param string $txHash хеш транзакции
     * @return Transaction
     *
     * @throws Exception если транзакция уже существует
     */
    public function deposit(int $userId, float $amount, string $txHash)
    {
        return DB::transaction(function () use ($userId, $amount, $txHash) {

            if (Transaction::where('tx_hash', $txHash)->exists()) {
                throw new Exception('Transaction already processed');
            }

            $wallet = Wallet::where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            $wallet->balance += $amount;
            $wallet->save();

            return Transaction::create([
                'user_id' => $userId,
                'type' => 'deposit',
                'amount' => $amount,
                'status' => 'confirmed',
                'tx_hash' => $txHash
            ]);
        });
    }

    /**
     * Данная функция выводит средства из кошелька пользователя.
     *
     * @param int $userId идентификатор пользователя
     * @param float $amount сумма для вывода
     * @return Transaction
     *
     * @throws Exception если баланс кошелька недостаточен
     */
    public function withdraw(int $userId, float $amount)
    {
        return DB::transaction(function () use ($userId, $amount) {

            $wallet = Wallet::where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($wallet->balance < $amount) {
                throw new Exception('Insufficient balance');
            }

            $wallet->balance -= $amount;
            $wallet->save();

            return Transaction::create([
                'user_id' => $userId,
                'type' => 'withdrawal',
                'amount' => $amount,
                'status' => 'pending'
            ]);
        });
    }

    /**
     * Списывает комиссию с кошелька пользователя.
     *
     * @param int $userId идентификатор пользователя
     * @param float $amount сумма комиссии для списания
     * @return Transaction
     *
     * @throws Exception если в кошелке недостаточно средств
     */
    public function chargeFee(int $userId, float $amount)
    {
        return DB::transaction(function () use ($userId, $amount) {

            $wallet = Wallet::where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($wallet->balance < $amount) {
                throw new Exception('Insufficient balance for fee');
            }

            $wallet->balance -= $amount;
            $wallet->save();

            return Transaction::create([
                'user_id' => $userId,
                'type' => 'fee',
                'amount' => $amount,
                'status' => 'confirmed'
            ]);
        });
    }
}
