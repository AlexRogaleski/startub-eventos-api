<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventoDonoTest extends TestCase
{
    use RefreshDatabase;

    public function test_vitrine_aberta_edicao_e_inscricoes_restritas_ao_dono(): void
    {
        $ana = User::factory()->create();
        $bia = User::factory()->create();
        $evento = ['titulo' => 'Evento', 'local' => 'Sala', 'data' => now()->addMonth()->format('Y-m-d H:i'), 'vagas' => 5, 'valor' => 0];

        Sanctum::actingAs($ana);
        $id = $this->postJson('/api/eventos', $evento)->assertCreated()->json('id');

        // vitrine: qualquer conta logada vê, com user_id para o front saber quem é o dono
        Sanctum::actingAs($bia);
        $this->getJson('/api/eventos')->assertOk()->assertJsonCount(1)->assertJsonPath('0.user_id', $ana->id);
        $this->getJson("/api/eventos/$id")->assertOk()->assertJsonPath('user_id', $ana->id);

        // alterar e remover: só o dono
        $this->patchJson("/api/eventos/$id", ['titulo' => 'Hack'])->assertForbidden();
        $this->deleteJson("/api/eventos/$id")->assertForbidden();

        // qualquer um se inscreve; a inscrição é de quem a fez
        $insc = $this->postJson("/api/eventos/$id/inscricoes", ['nome' => 'Bia', 'email' => 'bia@x.com', 'cpf' => '52998224725'])
            ->assertCreated()->assertJsonPath('user_id', $bia->id)->json('id');
        $this->getJson("/api/inscricoes/$insc")->assertOk();
        $this->getJson("/api/eventos/$id/inscricoes")->assertForbidden();

        Sanctum::actingAs($ana);
        $this->getJson("/api/inscricoes/$insc")->assertForbidden();
        $this->getJson("/api/eventos/$id/inscricoes")->assertOk()->assertJsonCount(1);
        $this->patchJson("/api/eventos/$id", ['titulo' => 'Novo'])->assertOk()->assertJson(['titulo' => 'Novo']);
        $this->deleteJson("/api/eventos/$id")->assertNoContent();
    }
}
