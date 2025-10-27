<x-app-layout>

<div class="min-h-screen bg-white py-12">
	<div class="max-w-5xl mx-auto px-6">

		<!-- Cart list -->
		<section class="space-y-8">
			@php
				// Assumptions: the controller passes $cartItems as a collection/array of items.
				// Each item: [ 'id' => .., 'product' => ['title','description','price','image'], 'quantity' => int ]
			@endphp

			@if(empty($cartItems) || count($cartItems) === 0)
				<div class="text-center text-gray-500 py-24">Your cart is empty.</div>
			@else
				@foreach($cartItems as $item)
				<div class="grid grid-cols-[40px_120px_1fr_120px] items-start gap-6 border-b pb-6">
					<!-- checkbox -->
					<div class="pt-6">
						<input type="checkbox" class="cart-checkbox h-5 w-5 rounded border-gray-300" 
							data-id="{{ $item['id'] ?? $item->id ?? '' }}"
							{{ ($item['is_selected'] ?? $item->is_selected ?? 0) == 1 ? 'checked' : '' }}>
					</div>

					<!-- image -->
					<div>
						<div class="w-28 h-20 bg-gray-200 rounded-md overflow-hidden">
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
					</div>

					<!-- details -->
					<div class="pt-2">
						<div class="text-lg font-semibold text-gray-800">{{ $item['product']['title'] ?? ($item->product->title ?? ($item->product->name ?? 'Product Title')) }}</div>
						<div class="text-sm text-gray-500 mt-1">{{ $item['product']['description'] ?? ($item->product->description ?? 'some product detail here') }}</div>
						<div class="text-gray-700 mt-4">{{ number_format($item['product']['price'] ?? ($item->product->price ?? 0), 0, '.', ',') }} baht</div>
					</div>

					<!-- qty + price area -->
					<div class="flex flex-col items-end pt-2">
						<div class="flex items-center gap-2 bg-white border rounded-full px-2 py-1">
							<button type="button" class="decrease inline-flex items-center justify-center w-7 h-7 text-gray-600" data-id="{{ $item['id'] ?? $item->id ?? '' }}">−</button>
							<input type="text" readonly class="qty w-12 text-center bg-transparent text-sm" value="{{ $item['quantity'] ?? ($item->quantity ?? 1) }}" data-id="{{ $item['id'] ?? $item->id ?? '' }}">
							<button type="button" class="increase inline-flex items-center justify-center w-7 h-7 text-gray-600" data-id="{{ $item['id'] ?? $item->id ?? '' }}">+</button>
						</div>

						<div class="text-sm text-gray-500 mt-6">&nbsp;</div>
					</div>
				</div>
				@endforeach
			@endif
		</section>

		<!-- Footer / totals -->
		<footer class="mt-10 border-t pt-6">
			<div class="flex items-center justify-between">
				<div class="flex items-center gap-3">
					<input id="select-all" type="checkbox" class="h-5 w-5 rounded border-gray-300">
					<label for="select-all" class="text-sm text-gray-600">Select All</label>
				</div>

				<div class="text-right">
					<div class="text-sm text-gray-600">{{ number_format($subtotal ?? 0, 0, '.', ',') }} baht</div>
					<div class="text-xs text-gray-500">Shipping Fee : {{ number_format($shipping ?? 50, 0, '.', ',') }} baht</div>
                    <div class="mt-3">
                        <form action="{{ route('cart.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="selected_items" id="selected_items" value="">
                            <button type="submit" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md">Check Out</button>
                        </form>
					</div>
				</div>
			</div>
		</footer>
	</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
	// Helper to parse formatted price like "4,500 baht" -> 4500
	function parsePriceFromElement(el){
		if(!el) return 0;
		const txt = el.textContent || el.innerText || '';
		const n = txt.replace(/[^0-9]/g, '');
		return parseInt(n || '0', 10);
	}

	// Calculate subtotal only from items where is_selected = 1
	function calculateSubtotal(){
		let total = 0;
		document.querySelectorAll('.cart-checkbox').forEach(cb => {
			if (cb.checked) {  // Only include if checkbox is checked (is_selected = 1)
				const id = cb.dataset.id;
				const qtyEl = document.querySelector(`.qty[data-id="${id}"]`);
				const priceEl = cb.closest('.grid').querySelector('.text-gray-700');
				const qty = qtyEl ? parseInt(qtyEl.value || '0', 10) : 0;
				const price = parsePriceFromElement(priceEl);
				total += qty * price;
			}
		});
		// Update subtotal display inside footer (first .text-sm.text-gray-600 in footer)
		const subtotalEl = document.querySelector('footer .text-sm.text-gray-600');
		if(subtotalEl) subtotalEl.textContent = total.toLocaleString() + ' baht';
		return total;
	}

	// Function to update cart item selection in database
	function updateCartItemSelection(cartId, isSelected) {
		return fetch(`/cart/${cartId}/select`, {
			method: 'PATCH',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
				'Accept': 'application/json'
			},
			body: JSON.stringify({ is_selected: isSelected ? 1 : 0 })
		}).then(res => {
			if(!res.ok) throw new Error('Network response was not ok');
			return res.json();
		});
	}

	// Function to update all cart items selection
	function updateAllCartItemsSelection(isSelected) {
		return fetch('/cart/select-all', {
			method: 'PATCH',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
				'Accept': 'application/json'
			},
			body: JSON.stringify({ is_selected: isSelected ? 1 : 0 })
		}).then(res => {
			if(!res.ok) throw new Error('Network response was not ok');
			return res.json();
		});
	}

	// Select all handling
	const selectAll = document.getElementById('select-all');
	const checkboxes = Array.from(document.querySelectorAll('.cart-checkbox'));

	if(selectAll){
		selectAll.addEventListener('change', function(){
			const checked = this.checked;
			updateAllCartItemsSelection(checked)
				.then(() => {
					checkboxes.forEach(cb => cb.checked = checked);
					calculateSubtotal();
				})
				.catch(err => {
					console.error(err);
					alert('Could not update selection status');
					window.location.reload();
				});
		});
	}

	// Update subtotal when checkboxes change
	checkboxes.forEach(cb => cb.addEventListener('change', function(){
		const cartId = this.dataset.id;
		const checked = this.checked;
		
		updateCartItemSelection(cartId, checked)
			.then(() => {
				// keep selectAll in sync
				if(selectAll){
					selectAll.checked = checkboxes.length > 0 && checkboxes.every(c => c.checked);
				}
				calculateSubtotal();
			})
			.catch(err => {
				console.error(err);
				alert('Could not update selection status');
				this.checked = !checked; // revert checkbox state
				calculateSubtotal();
			});
	}));

	// Increase / decrease handlers
	// When quantity is changed via + / - buttons, update DB via PATCH /cart/{id}
	function persistQuantity(cartId, quantity){
		// send PATCH request to /cart/{id}
		return fetch(`/cart/${cartId}`, {
			method: 'PATCH',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
				'Accept': 'application/json'
			},
			body: JSON.stringify({ quantity: quantity })
		}).then(res => {
			if(!res.ok) throw new Error('Network response was not ok');
			return res.json();
		});
	}

	document.querySelectorAll('.increase').forEach(btn => {
		btn.addEventListener('click', function(e){
			e.preventDefault();
			const id = this.dataset.id;
			const input = document.querySelector(`.qty[data-id="${id}"]`);
			if(!input) return;
			let v = parseInt(input.value || '0', 10);
			v = isNaN(v) ? 1 : v + 1;
			// optimistically update UI
			input.value = v;
			calculateSubtotal();
			// persist
			persistQuantity(id, v)
			.then(response => {
				if (response.status === 'success') {
					calculateSubtotal();
				} else {
					throw new Error('Update failed');
				}
			})
			.catch(err => {
				console.error(err);
				alert('Could not update quantity on server');
				window.location.reload();
			});
		});
	});

	document.querySelectorAll('.decrease').forEach(btn => {
		btn.addEventListener('click', function(e){
			e.preventDefault();
			const id = this.dataset.id;
			const input = document.querySelector(`.qty[data-id="${id}"]`);
			if(!input) return;
			let v = parseInt(input.value || '0', 10);
			v = isNaN(v) ? 1 : Math.max(1, v - 1);
			input.value = v;
			calculateSubtotal();
			persistQuantity(id, v).catch(err => {
				console.error(err);
				alert('Could not update quantity on server');
				window.location.reload();
			});
		});
	});

	// Handle checkout form submission
	const checkoutForm = document.querySelector('form[action*="checkout"]');
	if(checkoutForm){
		checkoutForm.addEventListener('submit', function(e){
			const hasSelectedItems = Array.from(document.querySelectorAll('.cart-checkbox')).some(cb => cb.checked);
			if(!hasSelectedItems){
				e.preventDefault();
				alert('Please select at least one item to checkout');
				return;
			}
		});
	}

	// Initial subtotal
	calculateSubtotal();
});
</script>
@endpush

</x-app-layout>
