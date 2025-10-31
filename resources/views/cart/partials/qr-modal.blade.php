<!-- QR Modal -->
<section>
    <div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">DEMONIC WAVE125i</h3>
                <button id="closeQRModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div class="flex justify-center">
                    <img src="{{ asset('images/cart/db-qrcode.png') }}" alt="QR Code" class="w-64 h-64 object-contain">
                </div>
                <div class="text-center space-y-2">
                    <p class="text-sm text-gray-600">Order Total</p>
                    <p class="text-xl font-bold">
                        {{ number_format($subtotal + $shipping, 0, '.', ',') }} baht
                    </p>
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
</section>