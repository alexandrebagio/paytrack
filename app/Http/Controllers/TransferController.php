<?php

namespace App\Http\Controllers;

use App\Enums\PersonType;
use App\Enums\TransferSituation;
use App\Jobs\ProcessTransfer;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends Controller
{
    #[Route('/api/transfer/', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $tranfers = Transfer::orderBy('created_at', 'desc')->paginate(25);

        return response()->json($tranfers);
    }

    #[Route('/api/transfer/', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'payee' => 'required|integer|exists:users,id',
                'value' => 'required|numeric|min:1|max:99999999999999999.99',
            ]);

            $value = (float) $request->value;

            $payer = User::find($request->user()->id);
            $walletPayer = $payer->wallet()->first();

            if ($request->user()->id === $request->payee) {
                throw new HttpException(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'It is not allowed to make transfers to yourself.'
                );
            }

            if ($payer->type === PersonType::Legal->value) {
                throw new HttpException(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'Action not permitted for retailers.'
                );
            }

            if (!$walletPayer || $walletPayer->cash < $value) {
                throw new HttpException(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'User without sufficient funds for transfer.'
                );
            }

            if (
                Transfer::where('payer', $request->user()->id)
                    ->where('payee', $request->payee)
                    ->where('value', $value)
                    ->where('situation', TransferSituation::Pending->value)
                    ->exists()
            ) {
                throw new HttpException(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'There is already a transfer of this value in processing.'
                );
            }

            if (
                Transfer::where('payer', $request->user()->id)
                    ->where('payee', $request->payee)
                    ->where('value', $value)
                    ->whereIn('situation', [TransferSituation::Pending->value, TransferSituation::Finish->value])
                    ->where('created_at', '>', Carbon::now()->subMinutes(10))
                    ->exists()
            ) {
                throw new HttpException(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'You have already made this transfer, please wait at least 10 minutes.'
                );
            }

            $walletPayer->update(['cash' => $walletPayer->cash - $value]);

            $walletPayee = User::find($request->payee)->wallet()->first();

            $transfer = Transfer::create([
                'wallet_payer' => $walletPayer->id,
                'wallet_payee' => $walletPayee->id,
                'value' => $value,
                'payer' => $request->user()->id,
                'payee' => $request->payee,
            ]);

            DB::commit();

            ProcessTransfer::dispatch($transfer)->onQueue('transfer');

            return response()->json([
                'message' => 'Processing transfer...',
                'data' => ['id' => $transfer->id],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    #[Route('/api/transfer/{id}', methods: ['GET'])]
    public function show(string $id, Request $request): JsonResponse
    {
        $request->merge(['id' => $id]);
        $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $transfer = Transfer::findOrFail($id);

        return response()->json(['data' => $transfer]);
    }
}
