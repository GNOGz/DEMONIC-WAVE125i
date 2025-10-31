<x-app-layout>
    <div class="max-w-5xl mx-auto px-6 py-8">
        {{-- Empty state --}}
        @if ($orders->isEmpty())
            <div class="p-8 bg-gray-50 rounded text-center">
                <p class="text-gray-600">You don't have any purchases yet.</p>
                <a href="{{ route('products.index') }}"
                   class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded">
                    Browse products
                </a>
            </div>
        @endif

        {{-- Order list --}}
        <div class="space-y-8">
            @foreach ($orders as $order)
                @php
                    $orderDate = optional($order->created_at)->format('d-m-Y');
                    $shippingFee = 50;

                    // Calculate subtotal from order items
                    $itemsSubtotal = $order->orderItems->sum(function ($it) {
                        $unit = $it->price ?? optional($it->product)->price ?? 0;
                        $qty = $it->quantity ?? 1;
                        return $unit * $qty;
                    });

                    $orderTotal = $order->total_price ?? ($itemsSubtotal + $shippingFee);
                @endphp

                <div class="border-t pt-4">
                    <div class="text-sm text-gray-700 mb-4">
                        <strong>Order ID:</strong> {{ $order->id }},
                        <strong>Purchased on:</strong> {{ $orderDate }}
                    </div>

                    {{-- Each order item --}}
                    @forelse ($order->orderItems as $item)
                        @php
                            $product = $item->product;
                            $name = $product->name;
                            $categoryName = $product->category->category_name ;
                            $imageFile = $product->image_url ?? null;
                            $imgPath = $imageFile ? asset('images/products/' . $categoryName . '/' . $imageFile) : null;
                            $unitPrice = $item->price ?? $product->price ?? 0;
                            $productName = $product->name ?? 'Unknown product';
                            $productDescription = $product->description ?? '';
                            $qty = $item->quantity ?? 1;
                        @endphp

                        <div class="flex items-start gap-6 mb-4">
                            {{-- Product Image --}}
                            <div class="w-36 h-36 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                                @if ($imgPath)
                                    <img src="{{ $imgPath }}" alt="{{ $productName }}" class="object-cover w-full h-full">
                                @else
                                    <div class="flex flex-col items-center justify-center text-center p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3v4M8 3v4m0 0h8" />
                                        </svg>
                                        <span class="text-sm text-gray-400">No image</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Product info --}}
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg">{{ $productName }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ \Illuminate\Support\Str::limit($productDescription, 120) }}
                                </p>

                                <div class="mt-3 flex items-center justify-between">
                                    <div class="text-lg font-medium">
                                        {{ number_format($unitPrice, 0) }} baht
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        x {{ $qty }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 bg-yellow-50 rounded text-sm text-gray-700">
                            No items in this order
                        </div>
                    @endforelse

                    {{-- Order summary --}}
                    <div class="border-t pt-3 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Shipping Fee: <strong>{{ number_format($shippingFee, 0) }} baht</strong>
                        </div>
                        <div class="text-sm text-gray-700">
                            Order Total: <strong>{{ number_format($orderTotal, 0) }} baht</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
