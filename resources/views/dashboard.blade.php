<x-app-layout>

    <div class="max-w-7xl mx-auto px-8 py-10">
        <h2 class="text-xl font-semibold mb-4">
            Welcome to the best wave125i’s accessories shop in Thailand!
        </h2>

        <div class="grid grid-cols-2 gap-8">
            <div class="text-gray-700 leading-relaxed text-lg">
                <p>We have all of accessories for your demonic wave.</p>
                <p>If you want a colorful rear suspension, we got it here.</p>
                <p>If you want a stylish seat for your wave, we got it here.</p>
                <p>If you want a cool frame, we also got it here.</p>
                <p>We have every part for you to custom your wave</p>
                <p>to make it more stylish.</p>
                <p>Browse our collection now and find the perfect accessories to make your
                    demonic wave125i truly stand out on the road!</p>
            </div>

            <div class="flex justify-center items-center">
                <img src="{{ asset('images/homepage/meowwave.png') }}"
                    alt="wave125i"
                    class="rounded-lg shadow-lg w-96">
            </div>
        </div>

        <h3 class="mt-16 text-xl font-bold">
            Our Recommend Products <span class="text-sm font-normal">(from each category)</span>
        </h3>

        <style>
            /* Hover: lift card and follow wishlist */
            .card-inner{transition:transform .15s ease, box-shadow .15s ease}
            .relative:hover .card-inner{transform:translateY(-4px);box-shadow:0 10px 15px rgba(0,0,0,0.08)}
            /* second-row: size equal to grid column on lg+ */
            /* small gap on mobile, larger on lg screens */
            .dashboard-second { display: flex; justify-content: center; gap: 24px; }
            .dashboard-second .card-wrapper { width: 100%; }
            @media (min-width: 1024px) {
                .dashboard-second { gap: 70px; }
                /* keep card width equal to one grid column (grid uses 45px column-gap => two gaps = 90px) */
                .dashboard-second .card-wrapper { width: calc((100% - 90px) / 3); }
            }
        </style>

    <!-- First row: same grid as products page -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-5 mb-8" style="column-gap:45px;">
            @foreach($recommendedProducts->take(3) as $product)
                <!-- Product card: id={{ $product->id }} -->
                <div class="relative">
                    <a href="{{ route('products.show', $product->id) }}" class="card-overlay absolute inset-0 z-10 focus:z-40 focus:outline-none" tabindex="0" aria-label="View details for {{ $product->name }}"></a>

                    <div class="card-inner border rounded-lg p-4 flex flex-col h-full transition-transform duration-150">
                        @if($product->image_url && $product->category)
                            <img src="{{ asset('images/products/' . $product->category->category_name . '/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-52 object-cover mb-4 rounded">
                        @endif

                        <div class="flex-1 flex flex-col">
                            <h4 class="font-bold text-lx mb-2 h-12 overflow-hidden">{{ $product->name }}</h4>
                            <div class="flex items-center justify-between mt-2">
                                @if($product->in_stock > 0)
                                    <p class="text-green-600 font-semibold text-xl">{{ $product->price }} bath</p>
                                @else
                                    <p class="text-red-600 font-semibold text-xl">Out of stock</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- wishlist removed on dashboard --}}
                </div>
                <!-- end product card -->
            @endforeach
        </div>

    <!-- Second row: center 2 items (match grid column width) -->
        <div class="dashboard-second">
            @foreach($recommendedProducts->skip(3) as $product)
                <div class="card-wrapper px-0">
                    <div class="relative">
                        <a href="{{ route('products.show', $product->id) }}" class="card-overlay absolute inset-0 z-10 focus:z-40 focus:outline-none" tabindex="0" aria-label="View details for {{ $product->name }}"></a>

                        <div class="card-inner border rounded-lg p-4 flex flex-col h-full transition-transform duration-150">
                            @if($product->image_url && $product->category)
                                <img src="{{ asset('images/products/' . $product->category->category_name . '/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-52 object-cover mb-4 rounded">
                            @endif

                            <div class="flex-1 flex flex-col">
                                <h4 class="font-bold text-lx mb-2 h-12 overflow-hidden">{{ $product->name }}</h4>
                                <div class="flex items-center justify-between mt-2">
                                    @if($product->in_stock > 0)
                                        <p class="text-green-600 font-semibold text-xl">{{ $product->price }} bath</p>
                                    @else
                                        <p class="text-red-600 font-semibold text-xl">Out of stock</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- wishlist removed on dashboard --}}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- wishlist removed on dashboard; functionality kept in products/index.blade.php -->

</x-app-layout>
