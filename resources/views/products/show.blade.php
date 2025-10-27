<x-app-layout>
    

    <div class="bg-white">
        <div class="bg-white max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white ">
                <div class="px-6">
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
                        <h3 class="text-xl  mb-4">{{ $product->name }}</h3>
                        <p class="text-base mb-4">{{ $product->description }}</p>
                        <p class="text-base mb-4">in stock : {{ $product->in_stock }}</p>
                        @if($product->in_stock > 0)
                            <p class="text-xl font-semibold text-green-600">Price : {{ $product->price }} bath</p>
                        @else
                            <p class="text-xl font-semibold text-red-600">Out of stock</p>
                        @endif
                    </div>

                    <!-- แถบเลือกจำนวน แบบ [-][ปริมาณ][+] -->
                    <style>
                        /* hide native number input spinners so only our -/+ buttons remain visible */
                        input[type=number]::-webkit-outer-spin-button,
                        input[type=number]::-webkit-inner-spin-button {
                            -webkit-appearance: none;
                            margin: 0;
                        }
                        input[type=number] { -moz-appearance: textfield; }
                    </style>
                    <form method="POST" action="{{ route('products.addToCart', $product->id) }}" class="flex justify-end items-center mb-6" id="add-to-cart-form">
                        @csrf
                        <!-- scaled down to 75% to reduce overall visual size; increased border thickness and maximum roundness -->
                        <div class="inline-flex items-center border-2 border-black rounded-lg  overflow-hidden mr-4 transform scale-75 origin-center" role="group" aria-label="Quantity selector">
                            <button type="button" class="qty-decrement h-10 px-3 bg-gray-50 hover:bg-gray-100 text-2xl text-black leading-none border-r-2 border-black " id="qty-decrement" aria-label="Decrease quantity" @if($product->in_stock == 0) disabled @endif>−</button>
                            <!-- input has no border; use button borders as internal dividers -->
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->in_stock }}" data-min="1" data-max="{{ $product->in_stock }}" class="h-10 w-16 text-center px-2 bg-white focus:outline-none" @if($product->in_stock == 0) disabled @endif>
                            <button type="button" class="qty-increment h-10 px-3 bg-gray-50 hover:bg-gray-100 text-2xl text-black leading-none border-l-2 border-black  " id="qty-increment" aria-label="Increase quantity" @if($product->in_stock == 0) disabled @endif>+</button>
                        </div>
                        <button type="submit" id="add-to-cart-btn" @if($product->in_stock == 0) disabled class="bg-[#1100FF] text-white font-bold py-3 px-6 rounded-lg opacity-50 cursor-not-allowed" @else class="bg-[#1100FF] text-white font-bold py-3 px-6 rounded-lg" @endif>
                            Add to Cart
                        </button>
                    </form>

                    <script>
                        (function(){
                            var qtyInput = document.getElementById('quantity');
                            var dec = document.getElementById('qty-decrement');
                            var inc = document.getElementById('qty-increment');
                            if (! qtyInput) return;
                            var min = parseInt(qtyInput.getAttribute('data-min') || 1, 10);
                            var max = parseInt(qtyInput.getAttribute('data-max') || 9999, 10);

                            function clamp(v){ v = parseInt(v, 10) || 0; if (v < min) return min; if (v > max) return max; return v; }

                            function updateButtons(){
                                var v = clamp(qtyInput.value);
                                qtyInput.value = v;
                                if (dec) dec.disabled = (v <= min);
                                if (inc) inc.disabled = (v >= max);
                            }

                            if (dec) dec.addEventListener('click', function(e){
                                e.preventDefault();
                                qtyInput.value = clamp((parseInt(qtyInput.value,10)||0) - 1);
                                updateButtons();
                            });
                            if (inc) inc.addEventListener('click', function(e){
                                e.preventDefault();
                                // ensure we do not exceed available stock (max)
                                var current = parseInt(qtyInput.value,10) || 0;
                                if (current >= max) return; // prevent increment beyond stock
                                qtyInput.value = clamp(current + 1);
                                updateButtons();
                            });

                            // allow manual input but validate on blur / change
                            qtyInput.addEventListener('change', updateButtons);
                            qtyInput.addEventListener('blur', updateButtons);

                            // ensure value is valid before submit and initialize
                            var form = document.getElementById('add-to-cart-form');
                            if (form) {
                                form.addEventListener('submit', function(e){
                                    // clamp value to allowed range before submitting
                                    qtyInput.value = clamp(qtyInput.value);
                                });
                            }
                            updateButtons();
                        })();
                    </script>

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