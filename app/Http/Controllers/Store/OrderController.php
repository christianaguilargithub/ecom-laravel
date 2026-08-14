<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items')
            ->latest()
            ->paginate(10);

        return Inertia::render('Store/Orders', ['orders' => $orders]);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items');

        return Inertia::render('Store/OrderDetail', ['order' => $order]);
    }

    public function store(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with(['items.product'])
            ->firstOrFail();

        abort_if($cart->items->isEmpty(), 422, 'Your cart is empty.');

        // Validate stock and calculate totals server-side
        foreach ($cart->items as $item) {
            abort_unless($item->product->is_active, 422, "{$item->product->name} is no longer available.");
            abort_if($item->quantity > $item->product->stock, 422, "Not enough stock for {$item->product->name}.");
        }

        $subtotal = $cart->items->sum(fn ($i) => $i->product->price * $i->quantity);
        $grandTotal = $subtotal; // shipping/tax to be added later

        $order = DB::transaction(function () use ($request, $cart, $subtotal, $grandTotal) {
            $order = Order::create([
                'user_id'          => $request->user()->id,
                'order_number'     => 'ORD-' . strtoupper(Str::random(8)),
                'status'           => 'pending',
                'payment_status'   => 'pending',
                'fulfillment_status' => 'unfulfilled',
                'subtotal'         => $subtotal,
                'shipping_total'   => 0,
                'discount_total'   => 0,
                'tax_total'        => 0,
                'grand_total'      => $grandTotal,
                'shipping_address' => [],
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'sku'          => $item->product->sku,
                    'unit_price'   => $item->product->price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->product->price * $item->quantity,
                ]);

                // Deduct stock
                $item->product->decrement('stock', $item->quantity);
            }

            $cart->update(['status' => 'completed']);

            return $order;
        });

        return to_route('orders.show', $order)->with('success', 'Order placed successfully!');
    }
}
