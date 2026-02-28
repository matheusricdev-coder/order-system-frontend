<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Order\FailPayment\FailPaymentCommand;
use App\Application\Order\FailPayment\FailPaymentHandler;
use App\Application\Order\FinalizePayment\FinalizePaymentCommand;
use App\Application\Order\FinalizePayment\FinalizePaymentHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

final class WebhookController extends Controller
{
    /**
     * POST /api/webhooks/stripe
     *
     * Receives Stripe events and dispatches them to the appropriate handler.
     * This endpoint is intentionally NOT behind auth:sanctum — Stripe calls it
     * directly. It is protected by webhook signature verification instead.
     *
     * Events handled:
     *   - payment_intent.succeeded  → FinalizePaymentHandler (consume stock, mark PAID)
     *   - payment_intent.payment_failed → FailPaymentHandler (release stock, mark CANCELLED)
     *
     * All other events are acknowledged with 200 and ignored to prevent Stripe
     * from retrying indefinitely.
     */
    public function handle(
        Request $request,
        FinalizePaymentHandler $finalizeHandler,
        FailPaymentHandler $failHandler,
    ): JsonResponse {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature', '');
        $secret    = (string) config('services.stripe.webhook_secret');

        // Verify the webhook signature to prevent spoofed events
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (UnexpectedValueException) {
            Log::warning('Stripe webhook: invalid payload received');
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException) {
            Log::warning('Stripe webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $intentId = $event->data->object->id ?? null;

        if ($intentId === null) {
            return response()->json(['status' => 'ignored'], 200);
        }

        match ($event->type) {
            'payment_intent.succeeded' => $finalizeHandler->handle(
                new FinalizePaymentCommand($intentId)
            ),
            'payment_intent.payment_failed' => $failHandler->handle(
                new FailPaymentCommand($intentId)
            ),
            default => Log::debug("Stripe webhook: unhandled event [{$event->type}]"),
        };

        return response()->json(['status' => 'ok'], 200);
    }
}
