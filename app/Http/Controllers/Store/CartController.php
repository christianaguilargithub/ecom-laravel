<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with(['items.product.category'])
            ->first();

        $items = $cart?->items ?? collect();

        $subtotal = $items->sum(fn ($i) => $i->product->price * $i->quantity);

        return Inertia::render('Store/Cart', [
            'items'    => $items,
            'subtotal' => number_format($subtotal, 2),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);

        abort_unless($product->is_active, 422, 'Product is not available.');
        abort_if($request->quantity > $product->stock, 422, 'Not enough stock.');

        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
            'status'  => 'active',
        ]);

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $newQty = $item->quantity + $request->quantity;
            abort_if($newQty > $product->stock, 422, 'Not enough stock.');
            $item->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
            ]);
        }

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        abort_unless($item->cart->user_id === $request->user()->id, 403);

        $request->validate(['quantity' => ['required', 'integer', 'min:1']]);

        abort_if($request->quantity > $item->product->stock, 422, 'Not enough stock.');

        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, CartItem $item)
    {
        abort_unless($item->cart->user_id === $request->user()->id, 403);

        $item->delete();

        return back()->with('success', 'Item removed.');
    }
}
