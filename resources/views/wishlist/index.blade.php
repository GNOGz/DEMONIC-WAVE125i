<x-app-layout>
    <div class="bg-white">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden">
                <div class="px-6 py-6">

                    @if($items->isEmpty())
                        <div class="p-8 bg-gray-50 rounded text-center">
                            <p class="text-gray-600">You haven't added any products to your wishlist yet.</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-y-6">
                        @foreach($items as $item)
                            @php $p = $item->product; @endphp
                            <div class="relative border rounded-lg p-4 flex gap-6 items-start card-outer">
                                <a href="{{ $p ? route('products.show', $p->id) : '#' }}"
                                   class="absolute inset-0 z-10 focus:z-40 focus:outline-none"
                                   aria-label="View {{ $p->name ?? 'product' }}"></a>

                                <div class="w-40 h-28 bg-gray-100 flex items-center justify-center rounded overflow-hidden">
                                    @if($p && $p->image_url)
                                        <img src="{{ asset('images/products/' . ($p->category->category_name ?? 'unknown') . '/' . $p->image_url) }}"
                                             alt="{{ $p->name }}" class="object-cover w-full h-full">
                                    @else
                                        <span class="text-sm text-gray-400">No image</span>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg">{{ $p->name ?? 'Unknown product' }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($p->description ?? '', 140) }}</p>
                                    <div class="mt-3">
                                        @if($p && $p->in_stock > 0)
                                            <span class="text-green-600 font-semibold">{{ number_format($p->price ?? 0) }} baht</span>
                                            <span class="ml-4 text-sm text-gray-500">left instock: {{ $p->in_stock ?? 'N/A' }}</span>
                                        @else
                                            <span class="text-red-600 font-semibold">Out of stock</span>
                                        @endif
                                    </div>
                                </div>

                                @php
                                    $isWished = true;
                                @endphp
                                <div class="z-20">
                                    <button type="button"
                                            class="wishlist-btn inline-flex items-center justify-center w-12 h-12 focus:outline-none border border-gray-300 rounded-full bg-white hover:bg-pink-50"
                                            data-id="{{ $p->id ?? '' }}"
                                            aria-label="Toggle wishlist"
                                            aria-pressed="{{ $isWished ? 'true' : 'false' }}">
                                        <span class="heart-icon text-2xl text-pink-500">♥</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CSRF meta — ส่วนใหญ่อยู่ใน layout แต่เช็คให้แน่ใจ --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- JS สำหรับ toggle (AJAX) — เหมือนใน products.index แต่เฉพาะหน้าทำงานบน wishlist --}}
    <script>
        (function(){
            var tokenMeta = document.querySelector('meta[name="csrf-token"]');
            var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

            function showToast(message, type) {
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
                setTimeout(function(){ t.remove(); }, 2500);
            }

            document.querySelectorAll('.wishlist-btn').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    var id = btn.dataset.id;
                    if (! id) return;

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
                        var action = json && json.action ? json.action : (json && json.message && json.message.toLowerCase().indexOf('remove') !== -1 ? 'removed' : 'added');
                        var name = (json && json.product_name) ? json.product_name : (json && json.message ? json.message : 'Product');
                        if (action === 'removed') {
                            // ลบ element ของ product ออกจากหน้า wishlist เพื่อ UX รู้สึกว่าเอาออกทันที
                            var outer = btn.closest('.card-outer');
                            if (outer) {
                                outer.remove();
                                showToast('Removed "' + name + '" from wishlist', 'info');
                            } else {
                                // fallback: เปลี่ยนไอคอน
                                if (heart) {
                                    heart.classList.remove('text-pink-500');
                                    heart.classList.add('text-gray-400');
                                }
                                showToast('Removed "' + name + '" from wishlist', 'info');
                            }
                        } else if (action === 'added') {
                            if (heart) {
                                heart.classList.remove('text-gray-400');
                                heart.classList.add('text-pink-500');
                            }
                            showToast('Added "' + name + '" to wishlist', 'success');
                        }
                    }).catch(function(err){
                        if (err.message === 'unauthenticated') {
                            window.location.href = '/login';
                            return;
                        }
                        console.error(err);
                        showToast('An error occurred', 'warn');
                    }).finally(function(){ btn.disabled = false; });
                });
            });
        })();
    </script>
</x-app-layout>
