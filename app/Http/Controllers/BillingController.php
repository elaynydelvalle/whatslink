<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Plan;
use App\Services\AsaasService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BillingController extends Controller
{
    public function subscribe(Request $request, string $planId, AsaasService $asaas)
    {
        $plan = Plan::find($planId);
        if (!$plan || !$plan->active) {
            return $this->fail('Plano não encontrado.', 404);
        }

        if ((float) $plan->price <= 0) {
            return $this->fail('Este plano é gratuito, não é necessário pagamento.');
        }

        $cpfCnpj = preg_replace('/\D/', '', $request->input('cpf_cnpj', ''));
        $user    = Auth::user();

        if (!$cpfCnpj && !$user->cpf_cnpj) {
            return $this->fail('Informe seu CPF ou CNPJ para gerar o boleto.');
        }

        try {
            $customerId = $asaas->ensureCustomer($user, $cpfCnpj ?: $user->cpf_cnpj);
            $payment    = $asaas->createBoleto($customerId, (float) $plan->price, "WhatsLink - Plano {$plan->name}");
        } catch (RuntimeException|ConnectionException $e) {
            Log::error('Falha ao gerar cobrança Asaas: ' . $e->getMessage());
            return $this->fail('Não foi possível gerar o boleto agora. Tente novamente em alguns minutos.', 502);
        }

        $charge = Charge::create([
            'user_id'          => $user->id,
            'plan_id'          => $plan->id,
            'asaas_payment_id' => $payment['id'],
            'status'           => $payment['status'] ?? 'PENDING',
            'amount'           => $plan->price,
            'boleto_url'       => $payment['bankSlipUrl'] ?? null,
            'invoice_url'      => $payment['invoiceUrl'] ?? null,
            'due_date'         => $payment['dueDate'] ?? null,
        ]);

        return response()->json(['ok' => true, 'data' => [
            'charge_id'   => $charge->id,
            'boleto_url'  => $charge->boleto_url,
            'invoice_url' => $charge->invoice_url,
            'due_date'    => $charge->due_date,
            'amount'      => $charge->amount,
        ]]);
    }

    public function webhook(Request $request)
    {
        $expected = config('services.asaas.webhook_token');
        if (!$expected || $request->header('asaas-access-token') !== $expected) {
            return response()->json(['ok' => false, 'error' => 'Token inválido.'], 401);
        }

        $event   = $request->input('event');
        $payment = $request->input('payment', []);
        $paymentId = $payment['id'] ?? null;

        if (!$paymentId) {
            return response()->json(['ok' => true, 'data' => null]);
        }

        $charge = Charge::where('asaas_payment_id', $paymentId)->first();
        if (!$charge) {
            return response()->json(['ok' => true, 'data' => null]);
        }

        $charge->update(['status' => $payment['status'] ?? $charge->status]);

        if (in_array($event, ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED'])) {
            $plan = Plan::find($charge->plan_id);
            if ($plan) {
                $charge->user->update(['plan_name' => $plan->name]);
            }
        }

        return response()->json(['ok' => true, 'data' => null]);
    }

    private function fail(string $msg, int $code = 400)
    {
        return response()->json(['ok' => false, 'error' => $msg], $code);
    }
}
