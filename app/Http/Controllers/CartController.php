<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class CartController extends Controller
{
    public function updateQuantity(Request $request, $cartId)
    {
        $request->validate([
            'action' => 'required|in:increment,decrement'
        ]);
        
        $user = Auth::user();
        $cartItem = $user->cart()->findOrFail($cartId);
        
        $product = $cartItem->product;
        $newQuantity = $request->action === 'increment' ? 
            $cartItem->quantity + 1 : 
            max(1, $cartItem->quantity - 1);

        if ($request->action === 'increment' && !$product->hasAvailableStock($newQuantity)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not enough stock available'
                ], 422);
            }
            return back()->with('error', 'Not enough stock available');
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();
        
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'quantity' => $cartItem->quantity,
                'subtotal' => $cartItem->quantity * $cartItem->product->price
            ]);
        }
        
        return back();
    }

    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cart()->with('product')->get();
            $allSelected = $cartItems->isNotEmpty() && $cartItems->every(fn($item) => $item->is_selected == 1);
        $subtotal = $cartItems->where('is_selected', 1)->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        $shipping = 50;
            return view('cart.index', compact('cartItems', 'subtotal', 'shipping', 'allSelected'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function updateCart(Request $request, $cartItemId)
    {
        $user = Auth::user();
        $quantity = $request->input('quantity', 1);

        $cartItem = $user->cart()->where('id', $cartItemId)->first();

        if ($cartItem) {
            $cartItem->quantity = $quantity;
            $cartItem->save();

            // If this is an AJAX request, return JSON so frontend can update without redirect
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'cart_item_id' => $cartItem->id,
                    'quantity' => $cartItem->quantity,
                    'product_price' => $cartItem->product->price ?? 0,
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Cart updated successfully!');
        }

        return redirect()->route('cart.index')->with('error', 'Cart item not found.');
    }

    // compatibility with resource route which expects update()
    public function update(Request $request, $id)
    {
        return $this->updateCart($request, $id);
    }

    public function removeFromCart($cartItemId)
    {
        $user = Auth::user(); 
        $cartItem = $user->cart()->where('id', $cartItemId)->first();
        if ($cartItem) {
            $cartItem->delete();

            return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
        }
        return redirect()->route('cart.index')->with('error', 'Cart item not found.');
    }

    public function clearCart()
    {
        $user = Auth::user();
        $user->cart()->delete();

        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully!');
    }

    public function updateSelection(Request $request, $cartItemId)
    {
        $user = Auth::user();
        $cartItem = $user->cart()->where('id', $cartItemId)->first();
        
        if (!$cartItem) {
            return response()->json(['status' => 'error', 'message' => 'Cart item not found'], 404);
        }

        try {
            $isSelected = $request->boolean('is_selected');
            $cartItem->is_selected = $isSelected;
            $cartItem->save();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'cart_item_id' => $cartItem->id,
                    'is_selected' => $cartItem->is_selected
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Selection updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating cart selection: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Failed to update selection'], 500);
            }

            return redirect()->route('cart.index')->with('error', 'Failed to update selection');
        }
    }

    public function updateAllSelection(Request $request)
    {
        $user = Auth::user();
        try {
            $isSelected = $request->boolean('is_selected');
            
            $user->cart()->update(['is_selected' => $isSelected]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'is_selected' => $isSelected
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Selections updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating all cart selections: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Failed to update selections'], 500);
            }

            return redirect()->route('cart.index')->with('error', 'Failed to update selections');
        }
    }

    public function toggle(Request $request)
    {
        $cartItem = Auth::user()->cart()->where('product_id', $request->product_id)->first();
        if(!$request->product_id){
            return back();
        }
        if ($cartItem) {
            $cartItem->delete();
        } else {
            Auth::user()->cart()->create([
                'product_id' => $request->product_id,
                'quantity' => 1,
            ]);
        }
        return back();
    }

    public function toggleAll(Request $request)
    {
        $user = Auth::user();
        $cartItems = $user->cart()->get();

        foreach ($cartItems as $cartItem) {
            $cartItem->delete();
        }

        return back();
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string'
        ]);

        $user = Auth::user();
        $cartItems = $user->cart()->where('is_selected', 1)->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'No items selected for checkout');
        }

        // Create new order
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'total_amount' => $cartItems->sum(function($item) {
                return $item->quantity * $item->product->price;
            })
        ]);

        // Create order items
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price
            ]);

            // Update product stock
            $product = $cartItem->product;
            $product->in_stock -= $cartItem->quantity;
            $product->save();

            // Remove item from cart
            $cartItem->delete();
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);
        
        // Check if product already exists in cart
        $existingItem = $user->cart()->where('product_id', $request->product_id)->first();
        
        $newQuantity = $existingItem ? 
            $existingItem->quantity + $request->quantity : 
            $request->quantity;

        // Validate against available stock
        if (!$product->hasAvailableStock($newQuantity)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not enough stock available'
                ], 422);
            }
            return back()->with('error', 'Not enough stock available');
        }

        if ($existingItem) {
            // Update quantity if product already in cart
            $existingItem->quantity = $newQuantity;
            $existingItem->save();
        } else {
            // Create new cart item
            $user->cart()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'is_selected' => 0
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Item added to cart']);
        }

        return redirect()->route('cart.index')->with('success', 'Item added to cart successfully!');
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        // If frontend sent selected_items JSON (with quantities), prefer that
        $selectedItemsPayload = json_decode($request->input('selected_items', '[]'), true);

        if (!empty($selectedItemsPayload) && is_array($selectedItemsPayload)) {
            // Map payload items (id + quantity) to actual cart items with products
            $cartItems = collect($selectedItemsPayload)->map(function($item) use ($user) {
                $cartItem = $user->cart()->with('product')->find($item['id'] ?? null);
                if ($cartItem) {
                    $cartItem->checkout_quantity = intval($item['quantity'] ?? $cartItem->quantity);
                }
                return $cartItem;
            })->filter();
        } else {
            // Fallback: use DB-selected items
            $cartItems = $user->cart()->with('product')->where('is_selected', 1)->get();
            // Set checkout quantity same as cart quantity for selected items
            $cartItems->each(function($item) {
                $item->checkout_quantity = $item->quantity;
            });
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Please select items to checkout');
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * ($item->checkout_quantity ?? $item->quantity);
        });
        $shipping = 50;
        $total = $subtotal + $shipping;

        return view('cart.checkout', compact('cartItems', 'subtotal', 'shipping', 'total', 'user'));
    }
    public function showCheckout()
    {
        $user = Auth::user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->where('selected', true) // ถ้ามี flag เลือก
            ->get();

        $subtotal = $cartItems->sum(fn($i) => $i->product->price * $i->quantity);
        $shipping = 0; // หรือคำนวณตามกติกา
        $total    = $subtotal + $shipping;

        // $address ดึงจากโปรไฟล์หรือโมเดล Address ของคุณ
        $address = $user->address ?? null;

        return view('cart.checkout', compact('cartItems','subtotal','shipping','total','address'));
    }

public function complete(Request $request)
{

    $user = Auth::user();
    $cart = $user->cart();
    $cartItem = $cart->where('is_selected', 1)->get();
    $randomNumber = random_int(1000, 9999);
    $order = Order::create([
        'user_id'          => $user->id,
        'order_item_id'  => $randomNumber,
    ]);
    foreach ($cartItem as $item) {
        // create order item
        OrderItem::create([
            'id'   => $order->id,
            'product_id' => $item->product_id,
            'quantity'   => $item->quantity,
        ]);

        // (optional) reduce stock if your products table has such a column
        if (isset($item->product->in_stock)) {
            $item->product->decrement('in_stock', $item->quantity);
        }

        // remove from cart
        $item->delete();
    }
    return redirect()->route('purchase.index')->with('success', 'Checkout completed.');
    
    // // 2) Fetch selected cart items with product
    // $cartItems = $user->cart()
    //     ->with('product')
    //     ->where('is_selected', 1)   // <— you already use is_selected elsewhere
    //     ->get();

    // if ($cartItems->isEmpty()) {
    //     return back()->with('error', 'No items selected for checkout');
    // }

    // // 3) Totals
    // $shipping = (int) $request->input('shipping_fee', 50); // adjust rule as you like
    // $subtotal = $cartItems->sum(fn($ci) => $ci->product->price * $ci->quantity);
    // $total    = $subtotal + $shipping;


    //     $order = Order::create([
    //         'user_id'          => $user->id,
    //         'product_id'       => $cartItems->product_id,
    //     ]);

    //     foreach ($cartItems as $ci) {
    //         // create order item
    //         OrderItem::create([
    //             'product_id' => $ci->product_id,
    //             'quantity'   => $ci->quantity,
    //         ]);

    //         // (optional) reduce stock if your products table has such a column
    //         if (isset($ci->product->in_stock)) {
    //             $ci->product->decrement('in_stock', $ci->quantity);
    //         }

    //         // remove from cart
    //         $ci->delete();
    //     }
    // return redirect()->route('purchase.index')->with('success', 'Checkout completed.');
}


}