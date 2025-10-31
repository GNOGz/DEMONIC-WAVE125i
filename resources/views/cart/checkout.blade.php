<x-app-layout>
    <div class="min-h-screen bg-white py-12">
        <div class="max-w-5xl mx-auto px-6">

            <!-- Main Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900">Check Out</h2>
                    
                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea 
                            name="shipping_address" 
                            id="shipping_address"
                            rows="4" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ $address->address_line ?? '' }}</textarea>
                    </div>

                    <!-- Shipping -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Shipping</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Order Total</span>
                                <span class="text-sm font-medium">{{ number_format($subtotal, 0, '.', ',') }} baht</span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-sm text-gray-600">Shipping Fee</span>
                                <span class="text-sm font-medium">{{ number_format($shipping, 0, '.', ',') }} baht</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment</label>
                        <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-center">
                            <span class="text-sm font-medium">Qr Payment</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Summary</h3>
                    
                    <!-- Cart Items -->

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('qrModal');
                    const showButton = document.getElementById('showQRButton');
                    const closeButton = document.getElementById('closeQRModal');
                    const shippingAddressInput = document.getElementById('shipping_address');
                    const finalShippingAddressInput = document.getElementById('finalShippingAddress');

                    showButton.addEventListener('click', function() {
                        // Update the hidden shipping address input
                        finalShippingAddressInput.value = shippingAddressInput.value;
                        // Show modal
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });

                    closeButton.addEventListener('click', function() {
                        modal.classList.remove('flex');
                        modal.classList.add('hidden');
                    });

                    // Close modal when clicking outside
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            modal.classList.remove('flex');
                            modal.classList.add('hidden');
                        }
                    });
                });
            </script>
                    <div class="space-y-4 mb-6">
                        @foreach($cartItems as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-gray-200 rounded overflow-hidden">
                                        @php $p = $item->product ?? null; @endphp
							    @if($p && (isset($p->image_url) || isset($p->image)))
								{{-- prefer image_url or image property depending on your model --}}
								@php $img = $p->image_url ?? $p->image ?? null; @endphp
								@if($img)
									<img src="{{ asset('images/products/' . ($p->category->category_name ?? 'default') . '/' . $img) }}" alt="{{ $p->name ?? $p->title ?? 'product' }}" class="object-cover w-full h-full">
								@endif
							@else
								<div class="w-full h-full bg-gray-300"></div>
							@endif
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <span class="font-medium">{{ number_format($item->product->price * $item->quantity, 0, '.', ',') }} baht</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Total -->
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center text-lg font-semibold">
                            <span>Total</span>
                            <span>{{ number_format($total, 0, '.', ',') }} baht</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-center">
                            <button id="showQRButton" type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Checkout
                            </button>
                    </div>

                <!-- QR Code Modal -->
                <div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                    <div class="bg-white p-8 rounded-lg max-w-md w-full mx-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-gray-900">DEMONIC WAVE125i</h3>
                            <button id="closeQRModal" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-center">
                                <img src="{{ asset('images/cart/db-qrcode.png') }}" alt="QR Code" class="w-64 h-64 object-contain">
                            </div>
                            <div class="text-center space-y-2">
                                <p class="text-sm text-gray-600">Order Total</p>
                                <p class="text-xl font-bold">{{ number_format($subtotal + $shipping, 0, '.', ',') }} baht</p>
                            </div>
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-600">After payment completed, please click below</p>
                                <form action="{{ route('cart.checkout.complete') }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="shipping_address" id="finalShippingAddress">
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                        Complete Checkout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Add any needed JavaScript here
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const addressField = document.getElementById('shipping_address');

            form.addEventListener('submit', function(e) {
                if (!addressField.value.trim()) {
                    e.preventDefault();
                    alert('Please enter your shipping address');
                }
            });
        });
    </script>
    @endpush>
</x-app-layout>
