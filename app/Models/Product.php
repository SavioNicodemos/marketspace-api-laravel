<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['name', 'description', 'is_new', 'price', 'accept_trade', 'user_id', 'is_active'];

    protected $casts = [
        'is_new' => 'boolean',
        'accept_trade' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * The payment methods that belong to the product
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The payment methods that belong to the product
     */
    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class);
    }

    /**
     * Get all the product's image.
     */
    public function productImages(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
