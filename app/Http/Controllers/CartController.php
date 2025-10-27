<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function updateQuantity(Request $request, $cartId)
    {
        $request->validate([
            'action' => 'required|in:increment,decrement'
        ]);
        
        $user = Auth::user();
        $cartItem = $user->cart()->findOrFail($cartId);
        
        if ($request->action === 'increment') {
            $cartItem->quantity += 1;
        } else {
            $cartItem->quantity = max(1, $cartItem->quantity - 1);
        }
        
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
        $subtotal = $cartItems->where('is_selected', 1)->sum(function($item) {
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
            
            return response()->json([
                'status' => 'success',
                'cart_item_id' => $cartItem->id,
                'is_selected' => $cartItem->is_selected
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating cart selection: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update selection'], 500);
        }
    }

    public function updateAllSelection(Request $request)
    {
        $user = Auth::user();
        try {
            $isSelected = $request->boolean('is_selected');
            
            $user->cart()->update(['is_selected' => $isSelected]);
            
            return response()->json([
                'status' => 'success',
                'is_selected' => $isSelected
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating all cart selections: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update selections'], 500);
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

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        
        // Check if product already exists in cart
        $existingItem = $user->cart()->where('product_id', $request->product_id)->first();
        
        if ($existingItem) {
            // Update quantity if product already in cart
            $existingItem->quantity += $request->quantity;
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
}