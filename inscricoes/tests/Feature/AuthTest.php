<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_cadastro_login_rota_protegida_e_logout(): void
    {
        $this->postJson('/api/register', ['name' => 'Ana', 'email' => 'ana@x.com', 'password' => 'segredo123', 'password_confirmation' => 'segredo123'])
            ->assertCreated()->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token'])->assertJsonMissingPath('user.password');

        $this->postJson('/api/login', ['email' => 'ana@x.com', 'password' => 'errada'])
            ->assertStatus(422)->assertJsonValidationErrors('email');

        $this->getJson('/api/eventos')->assertUnauthorized();
        $this->get('/api/eventos')->assertUnauthorized(); // sem Accept: application/json

        $token = $this->postJson('/api/login', ['email' => 'ana@x.com', 'password' => 'segredo123'])->assertOk()->json('token');
        $auth = ['Authorization' => "Bearer $token"];

        $this->getJson('/api/eventos', $auth)->assertOk();
        $this->getJson('/api/user', $auth)->assertOk()->assertJson(['email' => 'ana@x.com']);
        $this->postJson('/api/logout', [], $auth)->assertNoContent();

        $this->app['auth']->forgetGuards(); // o guard guarda o usuário entre requests no mesmo teste
        $this->getJson('/api/eventos', $auth)->assertUnauthorized();
    }
}
