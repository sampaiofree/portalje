<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('purchase_events')) {
            return;
        }

        Schema::create('purchase_events', function (Blueprint $table) {
            $table->string('id', 36)->primary();

            $table->bigInteger('creation_date')->nullable();
            $table->string('event')->nullable();
            $table->string('version')->nullable();

            $table->string('product_id')->nullable();
            $table->string('product_ucode')->nullable();
            $table->string('product_name')->nullable();
            $table->boolean('product_has_co_production')->nullable();

            $table->string('buyer_email')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_checkout_phone')->nullable();
            $table->string('buyer_document')->nullable();
            $table->string('buyer_address_zipcode')->nullable();
            $table->string('buyer_address_country')->nullable();
            $table->string('buyer_address_number')->nullable();
            $table->string('buyer_address_address')->nullable();
            $table->string('buyer_address_city')->nullable();
            $table->string('buyer_address_state')->nullable();
            $table->string('buyer_address_neighborhood')->nullable();
            $table->string('buyer_address_complement')->nullable();
            $table->string('buyer_address_country_iso')->nullable();

            $table->string('producer_name')->nullable();

            $table->decimal('commission_marketplace_value', 14, 2)->nullable();
            $table->string('commission_marketplace_currency')->nullable();
            $table->decimal('commission_producer_value', 14, 2)->nullable();
            $table->string('commission_producer_currency')->nullable();
            $table->decimal('commission_producer_converted_value', 14, 2)->nullable();
            $table->string('commission_producer_converted_currency')->nullable();
            $table->decimal('commission_producer_conversion_rate', 14, 6)->nullable();

            $table->bigInteger('purchase_approved_date')->nullable();
            $table->decimal('purchase_full_price_value', 14, 2)->nullable();
            $table->string('purchase_full_price_currency')->nullable();
            $table->decimal('purchase_original_offer_price_value', 14, 2)->nullable();
            $table->string('purchase_original_offer_price_currency')->nullable();
            $table->decimal('purchase_price_value', 14, 2)->nullable();
            $table->string('purchase_price_currency')->nullable();
            $table->string('purchase_offer_code')->nullable();
            $table->unsignedInteger('purchase_recurrence_number')->nullable();
            $table->string('purchase_subscription_anticipation_purchase')->nullable();
            $table->string('purchase_checkout_country_name')->nullable();
            $table->string('purchase_checkout_country_iso')->nullable();
            $table->text('purchase_origin_xcod')->nullable();
            $table->boolean('purchase_order_bump')->nullable();
            $table->string('purchase_order_bump_parent_transaction')->nullable();
            $table->bigInteger('purchase_order_date')->nullable();
            $table->bigInteger('purchase_date_next_charge')->nullable();
            $table->string('purchase_status')->nullable();
            $table->string('transaction')->nullable();
            $table->text('purchase_payment_billet_barcode')->nullable();
            $table->text('purchase_payment_billet_url')->nullable();
            $table->unsignedInteger('purchase_payment_installments_number')->nullable();
            $table->text('purchase_payment_pix_code')->nullable();
            $table->bigInteger('purchase_payment_pix_expiration_date')->nullable();
            $table->text('purchase_payment_pix_qrcode')->nullable();
            $table->text('purchase_payment_refusal_reason')->nullable();
            $table->string('purchase_payment_type')->nullable();

            $table->string('subscription_status')->nullable();
            $table->string('subscription_plan_id')->nullable();
            $table->string('subscription_plan_name')->nullable();
            $table->string('subscription_subscriber_code')->nullable();
            $table->string('affiliate_code')->nullable();
            $table->string('affiliate_name')->nullable();

            $table->string('atendimento')->nullable();

            $table->timestamps();

            $table->index('buyer_checkout_phone');
            $table->index('affiliate_code');
            $table->index('purchase_status');
            $table->index('transaction');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_events');
    }
};
