<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cart()->with('product')->get();
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        $shipping = 50;
        return view('cart.index', compact('cartItems', 'subtotal', 'shipping'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('products.show', compact('product'));
    }


    public function addToCart(Request $request, $productId)
    {
        $user = Auth::user();
        $quantity = $request->input('quantity', 1);

        $cartItem = $user->cart()->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $user->cartItems()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
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

    public function toggle(Request $request)
    {
        $cartItem = Auth::user()->cart()->where('product_id', $request->product_id)->first();
        if(!isset($cartItem[$product_id])){
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

    public function checkout(Request $request)
    {
        $user = Auth::user();
        $selectedItems = json_decode($request->input('selected_items', '[]'), true);
        
        if (empty($selectedItems)) {
            return redirect()->route('cart.index')->with('error', 'Please select items to checkout');
        }

        // Get cart items with products
        $cartItems = collect($selectedItems)->map(function($item) use ($user) {
            $cartItem = $user->cart()->with('product')->find($item['id']);
            if ($cartItem) {
                $cartItem->checkout_quantity = intval($item['quantity']);
            }
            return $cartItem;
        })->filter();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'No valid items found');
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->checkout_quantity;
        });
        $shipping = 50;
        $total = $subtotal + $shipping;

        return view('cart.checkout', compact('cartItems', 'subtotal', 'shipping', 'total', 'user'));
    }
}