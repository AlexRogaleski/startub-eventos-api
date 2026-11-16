<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Asaas
{
    private function http(): PendingRequest
    {
        return Http::baseUrl(config('services.asaas.url'))
            ->withHeaders([
                'access_token' => config('services.asaas.key'),
                'User-Agent' => 'inscricoes-workshop',
            ])
            ->acceptJson()
            ->timeout(15)
            ->throw();
    }

    public function criarCliente(string $nome, string $email, string $cpf): array
    {
        return $this->http()->post('/customers', [
            'name' => $nome,
            'email' => $email,
            'cpfCnpj' => $cpf,
            'notificationDisabled' => true,
        ])->json();
    }
    
    public function criarCobranca(
        string $clienteId,
        float $valor,
        string $descricao,
        string $referencia,
    ): array {
        return $this->http()->post('/payments', [
            'customer' => $clienteId,
            'billingType' => 'UNDEFINED',
            'value' => $valor,
            'dueDate' => now()->addDays(3)->toDateString(),
            'description' => $descricao,
            'externalReference' => $referencia,
        ])->json();
    }
}
