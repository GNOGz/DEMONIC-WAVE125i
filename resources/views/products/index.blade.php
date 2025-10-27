<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="border rounded-lg p-4 relative flex flex-col h-full">
                                @if($product->image_url && $product->category)
                                    <img src="{{ asset('images/products/' . $product->category->category_name . '/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover mb-4 rounded">
                                @endif

                                <div class="flex-1 flex flex-col">
                                    <h4 class="font-bold text-lg mb-2 h-12 overflow-hidden">{{ $product->name }}</h4>
                                    @if($product->in_stock > 0)
                                        <p class="text-green-600 font-semibold text-xl">฿{{ $product->price }}</p>
                                    @else
                                        <p class="text-red-600 font-semibold text-xl">Out of stock</p>
                                    @endif

                                    <div class="mt-auto flex justify-between items-center">
                                        <a href="{{ route('products.show', $product->id) }}" class="text-blue-500">View Details</a>
                                        @php
                                            $isWished = isset($wishlistIds) && in_array($product->id, $wishlistIds);
                                        @endphp
                                        <button type="button" class="wishlist-btn inline-flex items-center justify-center w-10 h-10 focus:outline-none border border-gray-300 rounded-full bg-white hover:bg-pink-50 transition-colors duration-150" data-id="{{ $product->id }}" aria-label="Toggle wishlist" aria-pressed="{{ $isWished ? 'true' : 'false' }}">
                                            <span class="heart-icon text-2xl @if($isWished) text-pink-500 @else text-gray-400 @endif">♥</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Inline wishlist JS (kept inside layout so it always runs) -->
    <script>
        (function(){
            var tokenMeta = document.querySelector('meta[name="csrf-token"]');
            var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

                    function showToast(message, type) {
                        // Remove any existing toasts created by this helper
                        var existing = document.querySelectorAll('.wishlist-toast');
                        existing.forEach(function(el){ el.remove(); });

                        var t = document.createElement('div');
                        t.textContent = message;
                        var base = 'wishlist-toast fixed bottom-6 right-6 px-4 py-2 rounded shadow-lg z-50 ';
                        if (type === 'success') t.className = base + 'bg-green-600 text-white';
                        else if (type === 'info') t.className = base + 'bg-blue-600 text-white';
                        else if (type === 'warn') t.className = base + 'bg-yellow-600 text-white';
                        else t.className = base + 'bg-gray-700 text-white';
                        document.body.appendChild(t);
                        setTimeout(function(){ t.remove(); }, 3000);
                    }

                    document.querySelectorAll('.wishlist-btn').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    var id = btn.dataset.id;
                    if (! id) return;
                    // Disable while processing
                    btn.disabled = true;
                    fetch('/products/' + id + '/wishlist', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    }).then(function(res){
                        if (res.status === 401) throw new Error('unauthenticated');
                        return res.json().catch(function(){ return {}; });
                    }).then(function(json){
                        var heart = btn.querySelector('.heart-icon');
                        if (! heart) return;
                        var action = json && json.action ? json.action : (json.message && json.message.toLowerCase().indexOf('remove') !== -1 ? 'removed' : 'added');
                        var name = (json && json.product_name) ? json.product_name : (json && json.message ? json.message : 'Product');
                        if (action === 'added') {
                            heart.classList.remove('text-gray-400');
                            heart.classList.add('text-pink-500');
                            btn.setAttribute('aria-pressed', 'true');
                            showToast('Added "' + name + '" to wishlist', 'success');
                        } else if (action === 'removed') {
                            heart.classList.remove('text-pink-500');
                            heart.classList.add('text-gray-400');
                            btn.setAttribute('aria-pressed', 'false');
                            showToast('Removed "' + name + '" from wishlist', 'info');
                        }
                    }).catch(function(err){
                        if (err.message === 'unauthenticated') {
                            // redirect to login page
                            window.location.href = '/login';
                            return;
                        }
                        console.error(err);
                    }).finally(function(){ btn.disabled = false; });
                });
            });
        })();
    </script>
</x-app-layout>