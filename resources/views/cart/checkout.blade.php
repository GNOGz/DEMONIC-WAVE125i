<x-app-layout>
    
    <div class="min-h-screen bg-white py-12">
        <div class="max-w-5xl mx-auto px-6">
            <!-- Header -->
            <header class="text-center mb-8">
                <h1 class="text-4xl font-extrabold text-blue-800">DEMONIC WAVE125i</h1>
                <h2 class="text-2xl font-semibold mt-4">Check Out</h2>
            </header>

            <!-- Address Section -->
            <section class="mb-8">
                <h3 class="text-lg font-semibold mb-4">Address</h3>
                <textarea
                    class="w-full p-4 border rounded-lg min-h-[100px]"
                    name="address"
                    placeholder="Enter your delivery address..."
                >{{ $user->address->full_address ?? '' }}</textarea>
            </section>

            <!-- Order Summary -->
            <section class="space-y-6">
                @foreach($selectedItems ?? [] as $item)
                    <div class="flex gap-6 border-b pb-6">
                        <!-- Product Image -->
                        <div class="w-28">
                            <div class="w-28 h-20 bg-gray-200 rounded-md overflow-hidden">
                                @if($item->product->image)
                                    <img src="{{ $item->product->image }}" alt="{{ $item->product->title }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">{{ $item->product->title }}</h4>
                            <p class="text-sm text-gray-500">{{ $item->product->description }}</p>
                            <div class="mt-2 flex justify-between items-center">
                                <div class="text-gray-700">{{ number_format($item->product->price, 0, '.', ',') }} baht</div>
                                <div class="text-sm text-gray-600">× {{ $item->quantity }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Totals -->
                <div class="border-t pt-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span>Shipping</span>
                        <span>{{ number_format($shipping ?? 50, 0) }} baht</span>
                    </div>
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Order Total</span>
                        <span>{{ number_format($total ?? 550, 0) }} baht</span>
                    </div>
                </div>
            </section>

            <!-- Payment Section -->
            <section class="mt-8">
                <h3 class="text-lg font-semibold mb-4">Payment : QR code</h3>
                <div class="bg-gray-100 rounded-lg p-8 flex justify-center">
                    <!-- Placeholder for QR code -->
                    <div class="w-48 h-48 bg-white rounded-lg flex items-center justify-center text-gray-400">
                        QR Code
                    </div>
                </div>
            </section>

            <!-- Checkout Button -->
            <div class="mt-8 text-center">
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="shipping_address" id="shipping_address">
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-md inline-block">
                        Check Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update hidden shipping address field before form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const addressText = document.querySelector('textarea[name="address"]').value;
            document.getElementById('shipping_address').value = addressText;
        });
    </script>
    @endpush
</x-app-layout>
