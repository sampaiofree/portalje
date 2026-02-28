<?php

namespace App\Http\Controllers;

use App\Models\PurchaseEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        if (!$this->isValidHottok($request, $payload)) {
            Log::warning('Hotmart webhook rejected: invalid hottok.', [
                'ip' => $request->ip(),
                'event' => data_get($payload, 'event'),
            ]);

            return response()->json(['message' => 'Unauthorized webhook'], 401);
        }

        $validator = Validator::make($payload, [
            'id' => ['nullable', 'string', 'max:64'],
            'event' => ['required', 'string', 'max:100'],
            'version' => ['nullable', 'string', 'max:20'],
            'creation_date' => ['nullable', 'numeric'],
            'data' => ['required', 'array'],
            'data.product' => ['required', 'array'],
            'data.buyer' => ['required', 'array'],
            'data.purchase' => ['required', 'array'],
            'data.purchase.transaction' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            Log::warning('Hotmart webhook rejected: invalid payload.', [
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'message' => 'Invalid webhook payload',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $transaction = (string) data_get($payload, 'data.purchase.transaction');
            $commissions = data_get($payload, 'data.commissions', []);
            $affiliate = data_get($payload, 'data.affiliates.0', []);

            [$marketplaceCommission, $producerCommission] = $this->resolveCommissions($commissions);

            $attributes = [
                'creation_date' => data_get($payload, 'creation_date'),
                'event' => data_get($payload, 'event'),
                'version' => data_get($payload, 'version'),

                'product_id' => data_get($payload, 'data.product.id'),
                'product_ucode' => data_get($payload, 'data.product.ucode'),
                'product_name' => data_get($payload, 'data.product.name'),
                'product_has_co_production' => data_get($payload, 'data.product.has_co_production'),

                'buyer_email' => data_get($payload, 'data.buyer.email'),
                'buyer_name' => data_get($payload, 'data.buyer.name'),
                'buyer_checkout_phone' => data_get($payload, 'data.buyer.checkout_phone'),
                'buyer_document' => data_get($payload, 'data.buyer.document'),
                'buyer_address_zipcode' => data_get($payload, 'data.buyer.address.zipcode'),
                'buyer_address_country' => data_get($payload, 'data.buyer.address.country'),
                'buyer_address_number' => data_get($payload, 'data.buyer.address.number'),
                'buyer_address_address' => data_get($payload, 'data.buyer.address.address'),
                'buyer_address_city' => data_get($payload, 'data.buyer.address.city'),
                'buyer_address_state' => data_get($payload, 'data.buyer.address.state'),
                'buyer_address_neighborhood' => data_get($payload, 'data.buyer.address.neighborhood'),
                'buyer_address_complement' => data_get($payload, 'data.buyer.address.complement'),
                'buyer_address_country_iso' => data_get($payload, 'data.buyer.address.country_iso'),

                'producer_name' => data_get($payload, 'data.producer.name'),

                'commission_marketplace_value' => data_get($marketplaceCommission, 'value'),
                'commission_marketplace_currency' => data_get($marketplaceCommission, 'currency_value'),
                'commission_producer_value' => data_get($producerCommission, 'value'),
                'commission_producer_currency' => data_get($producerCommission, 'currency_value'),
                'commission_producer_converted_value' => data_get($producerCommission, 'currency_conversion.converted_value'),
                'commission_producer_converted_currency' => data_get($producerCommission, 'currency_conversion.converted_to_currency'),
                'commission_producer_conversion_rate' => data_get($producerCommission, 'currency_conversion.conversion_rate'),

                'purchase_approved_date' => data_get($payload, 'data.purchase.approved_date'),
                'purchase_full_price_value' => data_get($payload, 'data.purchase.full_price.value'),
                'purchase_full_price_currency' => data_get($payload, 'data.purchase.full_price.currency_value'),
                'purchase_original_offer_price_value' => data_get($payload, 'data.purchase.original_offer_price.value'),
                'purchase_original_offer_price_currency' => data_get($payload, 'data.purchase.original_offer_price.currency_value'),
                'purchase_price_value' => data_get($payload, 'data.purchase.price.value'),
                'purchase_price_currency' => data_get($payload, 'data.purchase.price.currency_value'),
                'purchase_offer_code' => data_get($payload, 'data.purchase.offer.code'),
                'purchase_recurrence_number' => data_get($payload, 'data.purchase.recurrence_number'),
                'purchase_subscription_anticipation_purchase' => data_get($payload, 'data.purchase.subscription_anticipation_purchase'),
                'purchase_checkout_country_name' => data_get($payload, 'data.purchase.checkout_country.name'),
                'purchase_checkout_country_iso' => data_get($payload, 'data.purchase.checkout_country.iso'),
                'purchase_origin_xcod' => data_get($payload, 'data.purchase.origin.xcod') ?? data_get($payload, 'data.purchase.origin.sck'),
                'purchase_order_bump' => data_get($payload, 'data.purchase.order_bump.is_order_bump'),
                'purchase_order_bump_parent_transaction' => data_get($payload, 'data.purchase.order_bump.parent_purchase_transaction'),
                'purchase_order_date' => data_get($payload, 'data.purchase.order_date'),
                'purchase_date_next_charge' => data_get($payload, 'data.purchase.date_next_charge'),
                'purchase_status' => data_get($payload, 'data.purchase.status'),
                'purchase_payment_billet_barcode' => data_get($payload, 'data.purchase.payment.billet_barcode'),
                'purchase_payment_billet_url' => data_get($payload, 'data.purchase.payment.billet_url'),
                'purchase_payment_installments_number' => data_get($payload, 'data.purchase.payment.installments_number'),
                'purchase_payment_pix_code' => data_get($payload, 'data.purchase.payment.pix_code'),
                'purchase_payment_pix_expiration_date' => data_get($payload, 'data.purchase.payment.pix_expiration_date'),
                'purchase_payment_pix_qrcode' => data_get($payload, 'data.purchase.payment.pix_qrcode'),
                'purchase_payment_refusal_reason' => data_get($payload, 'data.purchase.payment.refusal_reason'),
                'purchase_payment_type' => data_get($payload, 'data.purchase.payment.type'),

                'subscription_status' => data_get($payload, 'data.subscription.status'),
                'subscription_plan_id' => data_get($payload, 'data.subscription.plan.id'),
                'subscription_plan_name' => data_get($payload, 'data.subscription.plan.name'),
                'subscription_subscriber_code' => data_get($payload, 'data.subscription.subscriber.code'),

                'affiliate_code' => data_get($affiliate, 'affiliate_code'),
                'affiliate_name' => data_get($affiliate, 'name'),
            ];

            $purchaseEvent = PurchaseEvent::updateOrCreate(
                ['transaction' => $transaction],
                $attributes
            );

            if ($purchaseEvent->wasRecentlyCreated && !empty(data_get($payload, 'id'))) {
                $purchaseEvent->id = (string) data_get($payload, 'id');
                $purchaseEvent->save();
            }

            return response()->json(['message' => 'Webhook processed'], 200);
        } catch (\Throwable $e) {
            Log::error('Webhook handling error.', [
                'message' => $e->getMessage(),
                'event' => data_get($payload, 'event'),
                'transaction' => data_get($payload, 'data.purchase.transaction'),
            ]);

            return response()->json([
                'message' => 'Error processing webhook',
            ], 500);
        }
    }

    private function isValidHottok(Request $request, array $payload): bool
    {
        $expectedHottok = trim((string) config('services.hotmart.hottok', env('HOTMART_HOTTOK', env('Hotmart_Hottok'))));

        if ($expectedHottok === '') {
            return true;
        }

        $incomingHottok = $this->resolveIncomingHottok($request, $payload);

        return $incomingHottok !== '' && hash_equals($expectedHottok, $incomingHottok);
    }

    private function resolveIncomingHottok(Request $request, array $payload): string
    {
        $candidates = [
            $request->header('x-hotmart-hottok'),
            $request->header('hottok'),
            $request->header('hotmart-hottok'),
            $request->input('hottok'),
            data_get($payload, 'hottok'),
            data_get($payload, 'data.hottok'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return '';
    }

    private function resolveCommissions(array $commissions): array
    {
        $marketplace = [];
        $producer = [];

        foreach ($commissions as $commission) {
            $source = data_get($commission, 'source');

            if ($source === 'MARKETPLACE' && $marketplace === []) {
                $marketplace = $commission;
                continue;
            }

            if ($source === 'PRODUCER' && $producer === []) {
                $producer = $commission;
            }
        }

        if ($marketplace === [] && isset($commissions[0]) && is_array($commissions[0])) {
            $marketplace = $commissions[0];
        }

        if ($producer === [] && isset($commissions[1]) && is_array($commissions[1])) {
            $producer = $commissions[1];
        }

        return [$marketplace, $producer];
    }
}
