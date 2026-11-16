<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InscricaoAsaasTest extends TestCase
{
    use RefreshDatabase;

    private function eventoPago(): Evento
    {
        $user = Sanctum::actingAs(User::factory()->create());

        return $user->eventos()->create(['titulo' => 'Minicurso', 'local' => 'Sala 1', 'data' => now()->addMonth(),
            'vagas' => 10, 'valor' => 25]);
    }

    public function test_inscricao_em_evento_pago_gera_cobranca(): void
    {
        config(['services.asaas.url' => 'https://api-sandbox.asaas.com/v3', 'services.asaas.key' => 'x']);
        Http::fake([
            'api-sandbox.asaas.com/v3/customers' => Http::response(['id' => 'cus_1'], 200),
            'api-sandbox.asaas.com/v3/payments' => Http::response(['id' => 'pay_1', 'invoiceUrl' => 'https://exemplo/i/1'], 200),
        ]);
        $e = $this->eventoPago();

        $this->postJson("/api/eventos/{$e->id}/inscricoes", ['nome' => 'Ana', 'email' => 'a@x.com', 'cpf' => '52998224725'])
            ->assertCreated()
            ->assertJson(['status' => 'pendente', 'asaas_cobranca_id' => 'pay_1', 'link_pagamento' => 'https://exemplo/i/1']);

        Http::assertSent(fn ($r) => $r->url() === 'https://api-sandbox.asaas.com/v3/payments'
            && $r['customer'] === 'cus_1' && $r['billingType'] === 'UNDEFINED' && $r->hasHeader('access_token', 'x'));
    }

    public function test_falha_no_asaas_desfaz_inscricao(): void
    {
        Http::fake(['*' => Http::response(['errors' => [['code' => 'invalid_cpfCnpj', 'description' => 'x']]], 400)]);
        $e = $this->eventoPago();

        $this->postJson("/api/eventos/{$e->id}/inscricoes", ['nome' => 'Ana', 'email' => 'a@x.com', 'cpf' => '52998224725'])
            ->assertStatus(502);
        $this->assertSame(0, Inscricao::count());
    }

    public function test_webhook_confirma_e_e_idempotente(): void
    {
        config(['services.asaas.webhook_token' => 'segredo']);
        $e = $this->eventoPago();
        $i = $e->inscricoes()->create(['nome' => 'Ana', 'email' => 'a@x.com', 'cpf' => '1', 'status' => 'pendente', 'asaas_cobranca_id' => 'pay_9']);
        $corpo = ['id' => 'evt_1', 'event' => 'PAYMENT_RECEIVED', 'payment' => ['id' => 'pay_9']];

        $this->postJson('/api/webhooks/asaas', $corpo)->assertStatus(401);
        $this->postJson('/api/webhooks/asaas', $corpo, ['asaas-access-token' => 'segredo'])->assertOk()->assertExactJson(['recebido' => true]);
        $this->assertSame('confirmada', $i->fresh()->status);
        $this->postJson('/api/webhooks/asaas', $corpo, ['asaas-access-token' => 'segredo'])->assertOk()->assertJson(['duplicado' => true]);
    }

    public function test_webhook_sem_token_configurado_recusa(): void
    {
        config(['services.asaas.webhook_token' => null]);
        $this->postJson('/api/webhooks/asaas', ['id' => 'evt_2'])->assertStatus(401);
    }
}
