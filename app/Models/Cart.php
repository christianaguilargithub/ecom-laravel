<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CartItem;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(CartItem::class); }

    public function totalQuantity(): int
    {
        return $this->items->sum('quantity');
    }

    public function subtotal(): float
    {
        return $this->items->sum(fn ($item) => $item->product->price * $item->quantity);
    }
}
