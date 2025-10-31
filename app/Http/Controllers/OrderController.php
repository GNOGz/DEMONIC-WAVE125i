<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Cart;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ดึง order ของ user คนนั้น พร้อม item และ product
        $orders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->get();

        return view('purchase.index', compact('orders'));
    }
    // public function store(Request $request)
    // {
    //     $user = Auth::user();

    //     $data = $request->validate([
    //         'shipping_address' => 'required|string|max:500',
    //     ]);

    //     // ดึงรายการในตะกร้า
    //     $cart = Cart::with('product')
    //         ->where('user_id', $user->id)
    //         ->where('selected', true)            // ถ้ามีสถานะเลือก
    //         ->get();

    //     if ($cart->isEmpty()) {
    //         return back()->withErrors('Cart is empty.');
    //     }

    //     $subtotal = $cart->sum(fn($i) => $i->product->price * $i->quantity);
    //     $shipping =  $request->input('shipping_fee', 0);  // หรือคำนวนเอง
    //     $total    = $subtotal + $shipping;

    //     // สร้างออเดอร์
    //     $order = Order::create([
    //         'user_id'          => $user->id,
    //         'shipping_address' => $data['shipping_address'],
    //         'subtotal'         => $subtotal,
    //         'shipping_fee'     => $shipping,
    //         'total'            => $total,
    //         'status'           => 'paid', // หรือ 'pending'
    //     ]);

    //     // บันทึกรายการสินค้า
    //     foreach ($cart as $ci) {
    //         Order::create([
    //             'order_id'   => $order->id,
    //             'product_id' => $ci->product_id,
    //             'price'      => $ci->product->price,
    //             'quantity'   => $ci->quantity,
    //         ]);
    //     }

    //     // ล้างตะกร้า (ตามที่ต้องการ)
    //     Cart::whereIn('id', $cartItems->pluck('id'))->delete();

    //     return redirect()->route('purchase.index')->with('success', 'Checkout completed.');
    // }
}
