<x-app-layout>
    

    <div class="py-12 bg-white">
        <div class="bg-white max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white ">
                <div class="p-6">
                    <!-- image -->
                        @if($product->image_url && $product->category)
                            <div class="flex justify-center mb-6">
                                <div class="w-80 h-100 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('images/products/' . $product->category->category_name . '/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endif

                    <!-- block detail -->
                    <div class="border-t-2 border-b-2 border-gray-300 py-6 mb-6">
                        <h3 class="text-2xl font-bold mb-4">{{ $product->name }}</h3>
                        <p class="text-gray-700 mb-4">{{ $product->description }}</p>
                        <p class="text-gray-700 mb-4">in stock: {{ $product->in_stock }}</p>
                        @if($product->in_stock > 0)
                            <p class="text-3xl font-semibold text-green-600">{{ $product->price }} bath</p>
                        @else
                            <p class="text-3xl font-semibold text-red-600">Out of stock</p>
                        @endif
                    </div>

                    <!-- แถบเลือกจำนวน -->
                    <form method="POST" action="{{ route('products.addToCart', $product->id) }}" class="flex justify-end items-center mb-6">
                        @csrf
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->in_stock }}" class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mr-4" @if($product->in_stock == 0) disabled @endif>
                        <button type="submit" @if($product->in_stock == 0) disabled class="bg-blue-500 text-white font-bold py-3 px-6 rounded-lg opacity-50 cursor-not-allowed" @else class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg" @endif>
                            Add to Cart
                        </button>
                    </form>

                    <!-- Pop-up messages (simple alert or nicer toast) -->
                    <script>
                        (function(){
                            // Prefer a simple JS alert for now. If session message exists, show it.
                            var success = @json(session('success'));
                            var error = @json(session('error'));
                            if (success) {
                                // small non-blocking toast-like element
                                var t = document.createElement('div');
                                t.textContent = success;
                                t.className = 'fixed bottom-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg';
                                document.body.appendChild(t);
                                setTimeout(function(){ t.remove(); }, 3500);
                            }
                            if (error) {
                                var t = document.createElement('div');
                                t.textContent = error;
                                t.className = 'fixed bottom-6 right-6 bg-red-600 text-white px-4 py-2 rounded shadow-lg';
                                document.body.appendChild(t);
                                setTimeout(function(){ t.remove(); }, 5000);
                            }
                        })();
                    </script>

                    <br>
                    <a href="{{ route('products.index') }}" class="text-blue-500 mt-4 inline-block">Back to Products</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>