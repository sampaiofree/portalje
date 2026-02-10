<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $table = 'abandoned_carts';

    protected $fillable = [
        'id', 'creation_date', 'event', 'version', 'affiliate', 
        'product_id', 'product_name', 'buyer_name', 'buyer_email', 'buyer_phone', 
        'offer_code', 'checkout_country_name', 'checkout_country_iso'
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
}
