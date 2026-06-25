<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AsaasService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.asaas.env') === 'production'
            ? 'https://api.asaas.com/v3'
            : 'https://sandbox.asaas.com/api/v3';
        $this->apiKey = (string) config('services.asaas.api_key');
    }

    private function http()
    {
        if (!$this->apiKey) {
            throw new RuntimeException('ASAAS_API_KEY não configurada.');
        }

        return Http::baseUrl($this->baseUrl)
            ->withHeaders(['access_token' => $this->apiKey])
            ->acceptJson();
    }

    /**
     * Garante que o usuário tem um cliente correspondente no Asaas, criando se necessário.
     */
    public function ensureCustomer(User $user, string $cpfCnpj): string
    {
        if ($user->asaas_customer_id) {
            return $user->asaas_customer_id;
        }

        $res = $this->http()->post('customers', [
            'name'       => $user->name,
            'email'      => $user->email,
            'cpfCnpj'    => preg_replace('/\D/', '', $cpfCnpj),
            'externalReference' => (string) $user->id,
        ]);

        if (!$res->successful()) {
            throw new RuntimeException('Falha ao criar cliente no Asaas: ' . $res->body());
        }

        $customerId = $res->json('id');
        $user->update(['asaas_customer_id' => $customerId, 'cpf_cnpj' => $cpfCnpj]);

        return $customerId;
    }

    /**
     * Cria uma cobrança via boleto para o cliente informado.
     */
    public function createBoleto(string $customerId, float $amount, string $description): array
    {
        $res = $this->http()->post('payments', [
            'customer'    => $customerId,
            'billingType' => 'BOLETO',
            'value'       => $amount,
            'dueDate'     => now()->addDays(3)->toDateString(),
            'description' => $description,
        ]);

        if (!$res->successful()) {
            throw new RuntimeException('Falha ao criar cobrança no Asaas: ' . $res->body());
        }

        return $res->json();
    }
}
