<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'rating'         => 'required|integer|between:1,5',
            'comment'        => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\Customer $customer */
        $customer = $request->user('sanctum');

        // Pastikan transaksi milik customer ini dan sudah selesai
        $transaction = Transaction::withoutGlobalScopes()
            ->where('id', $request->transaction_id)
            ->where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->firstOrFail();

        // Cek sudah pernah review belum
        if (Review::where('transaction_id', $transaction->id)->exists()) {
            return response()->json(['message' => 'Transaksi ini sudah pernah diberi ulasan.'], 422);
        }

        $review = Review::create([
            'transaction_id' => $transaction->id,
            'customer_id'    => $customer->id,
            'branch_id'      => $transaction->branch_id,
            'employee_id'    => $transaction->employee_id,
            'rating'         => $request->rating,
            'comment'        => $request->comment,
        ]);

        return response()->json(['message' => 'Ulasan berhasil dikirim.', 'review' => $review], 201);
    }
}
