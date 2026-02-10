<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Codigo_ref;

use Illuminate\Support\Str;

class PurchaseEvent extends Model
{
    use HasFactory;

    protected $table = 'purchase_events'; 

    protected $fillable = [
        'id', 'creation_date', 'event', 'version', 
        'product_id', 'product_ucode', 'product_name', 'product_has_co_production',
        'buyer_email', 'buyer_name', 'buyer_checkout_phone', 'buyer_document', 'buyer_address_zipcode', 'buyer_address_country', 
        'buyer_address_number', 'buyer_address_address', 'buyer_address_city', 'buyer_address_state', 
        'buyer_address_neighborhood', 'buyer_address_complement', 'buyer_address_country_iso',
        'producer_name',
        'commission_marketplace_value', 'commission_marketplace_currency', 
        'commission_producer_value', 'commission_producer_currency', 'commission_producer_converted_value', 
        'commission_producer_converted_currency', 'commission_producer_conversion_rate',
        'purchase_approved_date', 'purchase_full_price_value', 'purchase_full_price_currency', 
        'purchase_original_offer_price_value', 'purchase_original_offer_price_currency', 
        'purchase_price_value', 'purchase_price_currency', 'purchase_offer_code', 
        'purchase_recurrence_number', 'purchase_subscription_anticipation_purchase', 'purchase_checkout_country_name', 
        'purchase_checkout_country_iso', 'purchase_origin_xcod', 'purchase_order_bump', 
        'purchase_order_bump_parent_transaction', 'purchase_order_date', 'purchase_date_next_charge', 
        'purchase_status', 'transaction', 'purchase_payment_billet_barcode', 'purchase_payment_billet_url', 
        'purchase_payment_installments_number', 'purchase_payment_pix_code', 
        'purchase_payment_pix_expiration_date', 'purchase_payment_pix_qrcode', 'purchase_payment_refusal_reason', 
        'purchase_payment_type', 'subscription_status', 'subscription_plan_id', 
        'subscription_plan_name', 'subscription_subscriber_code', 'affiliate_code', 'affiliate_name',
        'atendimento',
        'created_at', 'updated_at'
    ];

    public $incrementing = false; // Important for UUIDs
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function codigo_ref()
    {
        return $this->belongsTo(Codigo_ref::class, 'affiliate_code', 'codigo_ref');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'product_id', 'codigo_id_hotmart');
    }

}
