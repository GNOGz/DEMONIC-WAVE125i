<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        $wishlistIds = [];
        if (auth()->check()) {
            $wishlistIds = Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray();
        }

        return view('products.index', compact('products', 'wishlistIds'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $quantity = $request->input('quantity', 1);

        if ($quantity > $product->in_stock) {
            return redirect()->back()->with('error', 'Requested quantity exceeds available stock.');
        }

        $cart = Cart::where('user_id', auth()->id())->where('product_id', $id)->first();

        if ($cart) {
            $newQuantity = $cart->quantity + $quantity;
            if ($newQuantity > $product->in_stock) {
                return redirect()->back()->with('error', 'Total quantity in cart would exceed available stock.');
            }
            $cart->quantity = $newQuantity;
            $cart->save();
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $id,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart.');
    }

    /**
     * Add product to authenticated user's wishlist.
     * Creates a wishlist entry if one does not already exist.
     */
    public function addToWishlist(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $userId = auth()->id();
        if (! $userId) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->back()->with('error', 'You must be logged in to add to wishlist.');
        }

        $wishlist = Wishlist::where('user_id', $userId)->where('product_id', $id)->first();
        if ($wishlist) {
            // remove from wishlist (toggle off)
            $wishlist->delete();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Removed from wishlist', 'action' => 'removed', 'product_name' => $product->name], 200);
            }
            return redirect()->back()->with('success', 'Removed "' . $product->name . '" from your wishlist.');
        }

        // add to wishlist (toggle on)
        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $id,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Added to wishlist', 'action' => 'added', 'product_name' => $product->name], 201);
        }

        return redirect()->back()->with('success', 'Added "' . $product->name . '" to your wishlist.');
    }
}
