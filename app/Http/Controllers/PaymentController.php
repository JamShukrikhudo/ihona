<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function showPaymentPortal()
    {
        return view('tenant.payment-portal');
    }

    public function createSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_id' => 'required|integer|exists:invoices,id',
        ]);

        $invoice = Invoice::query()
            ->whereKey($validated['invoice_id'])
            ->where('tenant_id', $request->user()->id)
            ->whereNotIn('status', ['paid', 'void'])
            ->firstOrFail();

        Stripe::setApiKey(config('stripe.secret_key'));
        $paymentIntent = PaymentIntent::create([
            'amount' => (int) round(((float) $invoice->amount) * 100),
            'currency' => 'usd',
            'metadata' => [
                'invoice_id' => (string) $invoice->id,
                'tenant_id' => (string) $invoice->tenant_id,
            ],
        ]);

        return response()->json(['clientSecret' => $paymentIntent->client_secret]);
    }

    public function handlePaymentSuccess(): JsonResponse
    {
        return response()->json([
            'message' => 'Payment received. Its status will update after provider verification.',
        ]);
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                (string) config('stripe.webhook_secret')
            );
        } catch (\UnexpectedValueException|SignatureVerificationException $exception) {
            Log::warning('Rejected invalid Stripe webhook', ['reason' => $exception->getMessage()]);

            return response()->json(['message' => 'Invalid webhook signature.'], Response::HTTP_BAD_REQUEST);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $this->recordSuccessfulPayment($event->data->object);
        }

        return response()->json(['received' => true]);
    }

    public function generateReceipt(Request $request, int $paymentId): JsonResponse
    {
        $payment = Payment::query()
            ->whereKey($paymentId)
            ->where('tenant_id', $request->user()->id)
            ->firstOrFail();

        return response()->json(['message' => 'Receipt generated', 'payment' => $payment]);
    }

    private function recordSuccessfulPayment(object $intent): void
    {
        $invoiceId = filter_var($intent->metadata->invoice_id ?? null, FILTER_VALIDATE_INT);
        $tenantId = filter_var($intent->metadata->tenant_id ?? null, FILTER_VALIDATE_INT);

        if (! $invoiceId || ! $tenantId || ($intent->status ?? null) !== 'succeeded') {
            Log::warning('Stripe payment intent has invalid application metadata', ['intent_id' => $intent->id ?? null]);

            return;
        }

        DB::transaction(function () use ($intent, $invoiceId, $tenantId) {
            $invoice = Invoice::query()->lockForUpdate()->find($invoiceId);

            if (! $invoice || $invoice->tenant_id !== $tenantId) {
                Log::warning('Stripe payment intent did not match an invoice owner', ['intent_id' => $intent->id]);

                return;
            }

            $expectedAmount = (int) round(((float) $invoice->amount) * 100);
            if ((int) $intent->amount_received !== $expectedAmount || strtolower((string) $intent->currency) !== 'usd') {
                Log::warning('Stripe payment amount did not match invoice', ['intent_id' => $intent->id]);

                return;
            }

            Payment::firstOrCreate(
                ['payment_intent_id' => $intent->id],
                [
                    'amount' => $invoice->amount,
                    'payment_date' => now(),
                    'status' => 'completed',
                    'payment_method' => 'stripe',
                    'tenant_id' => $invoice->tenant_id,
                    'invoice_id' => $invoice->id,
                ]
            );

            $invoice->update(['status' => 'paid']);
        });
    }
}
