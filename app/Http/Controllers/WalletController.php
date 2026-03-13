<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\WalletService;
use Illuminate\Routing\Controller;

/**
 * Контроллер для работы с кошельком пользователя.
 *
 * Обрабатывает депозит, вывод средств и списание комиссии.
 * Возвращает JSON с результатом транзакции.
 */
class WalletController extends Controller
{
    /**
     * @var WalletService
     */
    private WalletService $walletService;

    /**
     * WalletController constructor.
     *
     * @param WalletService $walletService
     */
    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Обрабатывает запрос пользователя на пополнение счета.
     *
     * Проверяет данные запроса и вызывает WalletService для зачисления средств на кошелек пользователя.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function deposit(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.00000001',
            'tx_hash' => 'required|string',
        ]);

        $transaction = $this->walletService->deposit(
            $request->user_id,
            $request->amount,
            $request->tx_hash
        );

        return response()->json($transaction);
    }

    /**
     * Обрабатывает запрос пользователя на вывод средств.
     *
     * Проверяет данные запроса и вызывает WalletService для создания ожидающей транзакции вывода средств.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function withdraw(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.00000001',
        ]);

        $transaction = $this->walletService->withdraw(
            $request->user_id,
            $request->amount
        );

        return response()->json($transaction);
    }

    /**
     * Списывает комиссию с кошелька пользователя.
     *
     * Проверяет данные запроса и вызывает WalletService для списания суммы комиссии.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function fee(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.00000001',
        ]);

        $transaction = $this->walletService->chargeFee(
            $request->user_id,
            $request->amount
        );

        return response()->json($transaction);
    }
}
