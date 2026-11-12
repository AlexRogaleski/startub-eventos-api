<p align="center">
  <img src="https://startub.appmake.com.br/startub-logo.jpg" alt="Congresso startUB 2026" width="520">
</p>

<h1 align="center">StartUB Eventos API</h1>

<p align="center">
  <strong>Desenvolvimento de APIs REST e Integração de Sistemas com Laravel</strong><br>
  Minicurso da trilha <strong>Backend</strong> do Congresso startUB 2026
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Auth-Sanctum-FF2D20" alt="Laravel Sanctum">
  <img src="https://img.shields.io/badge/Pagamentos-Asaas-0030B9" alt="Asaas">
  <img src="https://img.shields.io/badge/licen%C3%A7a-MIT-green" alt="Licença MIT">
</p>

<p align="center">
  <a href="https://startub.appmake.com.br"><strong>🌐 API no ar</strong></a> ·
  <a href="https://github.com/prof-marcosdavid/startub-eventos"><strong>⚛️ Trilha Frontend</strong></a>
</p>

---

## Sobre o minicurso

Neste minicurso você desenvolve, do zero, uma **API REST de eventos e inscrições** com Laravel. Ao longo do caminho você trabalha com regras de negócio, organização em serviços e integração com um sistema de pagamentos de verdade.

**Professor:** Alex Rogaleski Marques

Ele acontece ao mesmo tempo que o minicurso de **Frontend com React e TypeScript**, do professor Marcos David Souza Ramos. As duas turmas são independentes, mas a aplicação do Frontend consome esta API. É assim que as duas trilhas se encontram.

## O que vamos construir

- **Cadastro de eventos** com título, local, data, número de vagas e valor (eventos gratuitos e pagos).
- **Inscrições** com validação (CPF, um e-mail por evento) e controle de vagas.
- **Cobrança via [Asaas](https://www.asaas.com/)** (sandbox): uma inscrição em evento pago gera o link de pagamento.
- **Webhook**: o Asaas avisa a API quando o pagamento é confirmado, e a inscrição muda de status sozinha.
- **Login com Laravel Sanctum** (token Bearer): cadastro, login, logout e rotas protegidas.
- **Autorização com Policies**: cada usuário só altera os próprios eventos e só vê as próprias inscrições.
- **Testes automatizados** com PHPUnit.

## Organização do repositório

O conteúdo é publicado dia a dia:

| Pasta / arquivo | O que tem | Quando |
|---|---|---|
| Apostila 0 | Preparação do ambiente e conceitos iniciais | antes do curso |
| Apostila 1 | Roteiro do dia 1 | dia 1 |
| `dia-1/` | Projeto como ficou ao final do dia 1 (versão parcial) | fim do dia 1 |
| Apostila 2 | Roteiro do dia 2 | dia 2 |
| `dia-2/` | Projeto completo, continuação do dia 1 | fim do dia 2 |

> Perdeu alguma parte da aula? Comece pela pasta do dia anterior e siga a apostila do dia.

## API no ar

A versão final está publicada em **https://startub.appmake.com.br**. A página inicial traz a documentação completa: endpoints, campos, autenticação e códigos de erro.

- URL base: `https://startub.appmake.com.br/api`
- Teste rápido: [`GET /api/ping`](https://startub.appmake.com.br/api/ping)

### Endpoints

| Método | Rota | Acesso |
|---|---|---|
| `POST` | `/api/register`, `/api/login` | público |
| `GET` | `/api/user` · `POST /api/logout` | logado |
| `GET` | `/api/eventos`, `/api/eventos/{id}` | qualquer conta logada (vitrine) |
| `POST` | `/api/eventos` | qualquer conta logada (vira dona do evento) |
| `PATCH` `DELETE` | `/api/eventos/{id}` | só o dono do evento |
| `POST` | `/api/eventos/{id}/inscricoes` | qualquer conta logada |
| `GET` | `/api/eventos/{id}/inscricoes` | só o dono do evento |
| `GET` | `/api/inscricoes/{id}` | só quem se inscreveu |

Envie `Accept: application/json` em todas as requisições e `Authorization: Bearer <token>` nas rotas protegidas. Sem token, a resposta é `401`. Numa ação exclusiva do dono, a resposta é `403`.

```bash
# login: devolve { user, token }
curl -X POST https://startub.appmake.com.br/api/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"voce@exemplo.com","password":"sua-senha"}'

# chamada autenticada
curl https://startub.appmake.com.br/api/eventos \
  -H "Accept: application/json" -H "Authorization: Bearer SEU_TOKEN"
```

## Rodando na sua máquina

**Pré-requisitos:** PHP 8.3+ (com a extensão `pdo_sqlite`) e [Composer](https://getcomposer.org/). O banco é SQLite, então não precisa instalar MySQL.

```bash
cd dia-2                      # ou dia-1, para a versão parcial
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed    # responda "yes" para criar o banco SQLite
php artisan serve             # http://localhost:8000
```

### Asaas (opcional)

Eventos gratuitos funcionam sem configurar nada. Para testar eventos pagos, crie uma conta no [sandbox do Asaas](https://sandbox.asaas.com/) e preencha no `.env`:

```env
ASAAS_API_KEY=sua_chave_do_sandbox
ASAAS_WEBHOOK_TOKEN=um_token_secreto_qualquer
```

O webhook (`POST /api/webhooks/asaas`) só recebe avisos do Asaas se a API estiver acessível pela internet. Por isso, em localhost a confirmação automática do pagamento não acontece.

### Testes

```bash
php artisan test
```

## Trilha Frontend

A interface que consome esta API está em **[prof-marcosdavid/startub-eventos](https://github.com/prof-marcosdavid/startub-eventos)**: React + TypeScript, com componentes, formulários, estado e consumo de APIs REST.

## Licença

[MIT](LICENSE). Use, estude e adapte à vontade.
