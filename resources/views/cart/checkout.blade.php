<x-app-layout>
    <div class="min-h-screen bg-white py-12">
        <div class="max-w-5xl mx-auto px-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-semibold text-blue-600">DEMONIC WAVE125i</h1>
                <div class="flex items-center">
                    <span class="text-gray-600">{{ $user->email }}</span>
                    <img src="{{ $user->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="Profile" class="w-8 h-8 rounded-full ml-2">
                </div>
            </div>

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
                            <span class="text-sm text-gray-600">QR code</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Summary</h3>
                    
                    <!-- Cart Items -->
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

                    <!-- Checkout Button -->
                    <form action="{{ route('cart.payment') }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-3 hover:bg-blue-700 transition-colors">
                            Check Out
                        </button>
                    </form>
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
