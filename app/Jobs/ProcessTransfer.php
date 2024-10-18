<?php

namespace App\Jobs;

use App\Enums\TransferSituation;
use App\Models\Transfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ProcessTransfer implements ShouldQueue
{
    use Queueable;


    public function __construct(
        private Transfer $transfer
    ) {
        //
    }

    public function handle(): void
    {
        DB::beginTransaction();

        try {
            $response = Http::get(env('ENDPOINT_TRANSFER_AUTHORIZE'));

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new \Exception('Failed to authorize transfer with endpoint.');
            }

            $body = json_decode($response->getBody());

            if ($body->status !== 'success') {
                throw new \Exception('Failed to authorize transfer with endpoint.');
            }

            $this->transfer->update([
                'situation' => TransferSituation::Finish->value,
            ]);

            $walletPayee = $this->transfer->wallet_payee()->first();
            $walletPayee->update(['cash' => $walletPayee->cash + $this->transfer->value]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->transfer->update([
                'error' => true,
                'error_message' => $th->getMessage(),
                'situation' => TransferSituation::Error->value,
            ]);

            $walletPayer = $this->transfer->wallet_payer()->first();
            $walletPayer->update(['cash' => $walletPayer->cash + $this->transfer->value]);

            throw $th;
        }
    }
}
