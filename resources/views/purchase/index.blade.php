<x-app-layout>
    <div class="max-w-5xl mx-auto px-6 py-8">

        @if ($orders->isEmpty())
            <div class="p-8 bg-gray-50 rounded text-center">
                <p class="text-gray-600">You don't have any purchases yet.</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded">Browse products</a>
            </div>
        @endif

        <div class="space-y-8">
            @foreach ($orders as $order)
                {{-- If you store shipping_fee and total_price in order table, use them; otherwise calculate --}}
                @php
                    $orderDate = $order->created_at ? $order->created_at->format('d-m-Y') : '';
                    // If you have fields: shipping_fee, total_price, use them. Otherwise compute:
                    $shippingFee = $order->shipping_fee ?? 0;
                    // Compute subtotal of items: price * qty
                    $itemsSubtotal = $order->items->sum(function($it){
                        return ($it->price ?? ($it->product->price ?? 0)) * ($it->quantity ?? 1);
                    });
                    $orderTotal = $order->total_price ?? ($itemsSubtotal + $shippingFee);
                @endphp

                <div class="border-t pt-4">
                    <div class="text-sm text-gray-700 mb-4">
                        <strong>Order ID :</strong> {{ $order->id }} , <strong>Purchased on</strong> {{ $orderDate }}
                    </div>

                    {{-- items (ถ้ามีหลายชิ้น ให้ลูป) --}}
                    @foreach ($order->items as $item)
                        @php
                            $p = $item->product;
                            $imgPath = $p && $p->image_url ? asset('images/products/' . ($p->category->category_name ?? 'unknown') . '/' . $p->image_url) : null;
                            $unitPrice = $item->price ?? ($p->price ?? 0);
                        @endphp

                        <div class="flex items-start gap-6 mb-4">
                            <div class="w-36 h-36 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                                @if($imgPath)
                                    <img src="{{ $imgPath }}" alt="{{ $p->name ?? 'Product' }}" class="object-cover w-full h-full">
                                @else
                                    <span class="text-sm text-gray-400">No image</span>
                                @endif
                            </div>

                            <div class="flex-1">
                                <h3 class="font-semibold text-lg">{{ $p->name ?? 'Unknown product' }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($p->description ?? '', 120) }}</p>

                                <div class="mt-3 flex items-center justify-between">
                                    <div class="text-lg font-medium">
                                        {{ number_format($unitPrice, 0) }} baht
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        x {{ $item->quantity ?? 1 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="border-t pt-3 flex items-center justify-between">
                        <div class="text-sm text-gray-700">Shipping Fee : <strong>{{ number_format($shippingFee, 0) }} baht</strong></div>
                        <div class="text-sm text-gray-700">Order Total : <strong>{{ number_format($orderTotal, 0) }} baht</strong></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- pagination --}}
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
