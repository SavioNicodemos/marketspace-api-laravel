<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\UuidTrait;

class Product extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['name', 'description', 'is_new', 'price', 'accept_trade', 'user_id', 'is_active'];

    /**
     * The payment methods that belong to the product
     */
    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class);
    }
}
