<x-app-layout>
    <div class="py-12 bg-white">
        <!-- Narrower page wrapper to reduce horizontal (x) size -->
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden ">
                <!-- Slightly less padding inside the wrapper to reduce horizontal space visually -->
                <div class="p-6 text-gray-900">
                          <style>
                                /* apply hover effect when the card container (.relative) is hovered.
                                    Using the parent :hover means hovering the wishlist (a child) will keep the
                                    hover state active for the card. */
                                .card-inner{transition:transform .15s ease, box-shadow .15s ease}
                                .relative:hover .card-inner{transform:translateY(-4px);box-shadow:0 10px 15px rgba(0,0,0,0.08)}

                                /* make wishlist button visually follow the card hover (lift + shadow)
                                    using the parent hover so the button remains in sync even when hovered */
                                .wishlist-btn{transition:transform .15s ease, box-shadow .15s ease, background-color .15s ease}
                                .relative:hover .wishlist-btn{transform:translateY(-4px) scale(1.02); box-shadow:0 8px 14px rgba(0,0,0,0.08)}
                          </style>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-5" style="column-gap:45px;">
                        @foreach($products as $product)
                                <!-- Product Card START: id={{ $product->id }} name="{{ $product->name }}" -->
                            <!-- wrapper holds overlay, card-inner and wishlist button as siblings so we can target hover only when overlay is hovered -->
                            <div class="relative">
                                <a href="{{ route('products.show', $product->id) }}" class="card-overlay absolute inset-0 z-10 focus:z-40 focus:outline-none" tabindex="0" aria-label="View details for {{ $product->name }}"></a>

                                <div class="card-inner border rounded-lg p-4 flex flex-col h-full transition-transform duration-150">
                                    @if($product->image_url && $product->category)
                                        <img src="{{ asset('images/products/' . $product->category->category_name . '/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-50 object-cover mb-4 rounded">
                                    @endif

                                    <div class="flex-1 flex flex-col">
                                        <h4 class="font-bold text-lg mb-2 h-12 overflow-hidden">{{ $product->name }}</h4>
                                        <div class="flex items-center justify-between mt-2">
                                            @if($product->in_stock > 0)
                                                <p class="text-green-600 font-semibold text-xl">{{ $product->price }} bath</p>
                                            @else
                                                <p class="text-red-600 font-semibold text-xl">Out of stock</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $isWished = isset($wishlistIds) && in_array($product->id, $wishlistIds);
                                @endphp
                                <!-- Wishlist is positioned above the overlay so it remains clickable; hovering wishlist won't trigger the overlay hover. -->
                                <button type="button" class="wishlist-btn z-20 absolute bottom-3 right-3 inline-flex items-center justify-center w-10 h-10 focus:outline-none border border-gray-300 rounded-full bg-white hover:bg-pink-50 transition-colors duration-150" data-id="{{ $product->id }}" aria-label="Toggle wishlist" aria-pressed="{{ $isWished ? 'true' : 'false' }}">
                                    <span class="heart-icon text-2xl @if($isWished) text-pink-500 @else text-gray-400 @endif">♥</span>
                                </button>
                            </div>
                            <!-- Product Card END: id={{ $product->id }} -->
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