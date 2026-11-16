<?php

namespace App\Http\Controllers;

use App\Models\Inscricao;
use App\Models\Notificacao;
use Illuminate\Http\Request;

class AsaasWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        // 1. Quem está chamando? Só o Asaas conhece o token.
        $token = config('services.asaas.webhook_token');
        $enviado = (string) $request->header('asaas-access-token');

        if (! $token || ! hash_equals($token, $enviado)) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        // 2. Já processamos este evento? O Asaas pode enviar o mesmo mais de uma vez.
        $notificacao = Notificacao::firstOrCreate(
            ['asaas_evento_id' => $request->input('id')],
            ['tipo' => $request->input('event'), 'payload' => $request->all()],
        );

        if (! $notificacao->wasRecentlyCreated) {
            return response()->json(['recebido' => true, 'duplicado' => true]);
        }

        // 3. A qual inscrição a cobrança pertence?
        $cobrancaId = $request->input('payment.id');
        $inscricao = Inscricao::where('asaas_cobranca_id', $cobrancaId)->first();

        if ($inscricao) {
            $tipo = $request->input('event');
            $pago = in_array($tipo, ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED']);
            $cancelado = in_array($tipo, ['PAYMENT_DELETED', 'PAYMENT_REFUNDED']);

            if ($pago && $inscricao->status !== 'confirmada') {
                $inscricao->update(['status' => 'confirmada', 'pago_em' => now()]);
            }

            if ($cancelado) {
                $inscricao->update(['status' => 'cancelada']);
            }
        }

        return response()->json(['recebido' => true]);
    }
}
