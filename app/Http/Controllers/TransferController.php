<?php

namespace App\Http\Controllers;

use App\Enums\PersonType;
use App\Enums\TransferSituation;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TransferController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'payer' => 'required|integer|exists:users,id',
                'payee' => 'required|integer|exists:users,id',
                'value' => 'required|numeric|min:1|max:99999999999999999.99',
            ]);

            $value = (float) $request->value;

            $payer = User::find($request->payer);
            $walletPayer = $payer->wallet()->first();

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
                Transfer::where($request->only('payer', 'payee'))
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
                Transfer::where($request->only('payer', 'payee'))
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

            $walletPayee = User::find($request->payee)->wallet()->first();

            $transfer = Transfer::create([
                'wallet_payer' => $walletPayer->id,
                'wallet_payee' => $walletPayee->id,
                'value' => $value,
                ...$request->only('payer', 'payee'),
            ]);

            DB::commit();

            // TODO Serviço em fila para tranferência

            return [
                'message' => 'Processing transfer...',
                'id' => $transfer->id,
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(Transfer $transfer)
    {
        //
    }
}
