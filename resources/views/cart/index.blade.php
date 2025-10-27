<x-app-layout>
<meta name="csrf-token" content="{{ csrf_token() }}">

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
				<div class="flex items-center space-x-4 border-b pb-6">
					<!-- checkbox -->
					<div>
						<input type="checkbox" class="cart-checkbox h-5 w-5 rounded border-gray-300" 
							data-id="{{ $item['id'] ?? $item->id ?? '' }}"
							{{ ($item['is_selected'] ?? $item->is_selected ?? 0) == 1 ? 'checked' : '' }}>
					</div>

					<!-- image -->
					<div>
						<div class="w-28 h-24 bg-gray-200 rounded-md overflow-hidden">
							@php $p = $item->product ?? null; @endphp
							@if($p && (isset($p->image_url) || isset($p->image)))
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
					<div class="flex-grow">
						<div class="text-lg font-semibold text-gray-800">{{ $item['product']['title'] ?? ($item->product->title ?? ($item->product->name ?? 'Product Title')) }}</div>
						<div class="text-sm text-gray-500 mt-1">{{ $item['product']['description'] ?? ($item->product->description ?? 'some product detail here') }}</div>
						<div class="text-gray-700 mt-4">{{ number_format($item['product']['price'] ?? ($item->product->price ?? 0), 0, '.', ',') }} baht</div>
					</div>

					<!-- quantity controls -->
					<div class="flex items-center">
						<div class="inline-flex items-center border-2 border-black rounded-lg overflow-hidden" role="group" aria-label="Quantity selector">
							<form method="POST" action="{{ route('cart.updateQuantity', $item->id) }}" class="quantity-form" data-cart-id="{{ $item->id }}">
								@csrf
								<input type="hidden" name="action" value="decrement">
								<button type="submit" class="qty-decrement h-8 w-8 bg-gray-50 hover:bg-gray-100 text-xl text-black leading-none border-r-2 border-black">−</button>
							</form>
							
							<span class="quantity-display h-8 w-12 text-center bg-white leading-8">{{ $item->quantity }}</span>
							
							<form method="POST" action="{{ route('cart.updateQuantity', $item->id) }}" class="quantity-form" data-cart-id="{{ $item->id }}">
								@csrf
								<input type="hidden" name="action" value="increment">
								<button type="submit" class="qty-increment h-8 w-8 bg-gray-50 hover:bg-gray-100 text-xl text-black leading-none border-l-2 border-black">+</button>
							</form>
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
	// Handle cart item selection
	document.querySelectorAll('.cart-checkbox').forEach(checkbox => {
		checkbox.addEventListener('change', async function() {
			const cartId = this.dataset.id;
			const isSelected = this.checked;

			try {
				const response = await fetch(`/cart/${cartId}/select`, {
					method: 'PATCH',
					headers: {
						'Content-Type': 'application/json',
						'Accept': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
					},
					body: JSON.stringify({
						is_selected: isSelected
					})
				});

				if (!response.ok) throw new Error('Network response was not ok');
				
				const data = await response.json();
				if (data.status === 'success') {
					// Update select all checkbox if needed
					const selectAll = document.getElementById('select-all');
					if (selectAll) {
						const checkboxes = document.querySelectorAll('.cart-checkbox');
						selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
					}
				} else {
					// Revert checkbox if update failed
					this.checked = !isSelected;
				}
			} catch (error) {
				console.error('Error:', error);
				// Revert checkbox if update failed
				this.checked = !isSelected;
			}
		});
	});

	// Handle select all checkbox
	const selectAll = document.getElementById('select-all');
	if (selectAll) {
		selectAll.addEventListener('change', async function() {
			const isSelected = this.checked;
			const checkboxes = document.querySelectorAll('.cart-checkbox');
			let success = true;

			// Try to update all items
			for (const checkbox of checkboxes) {
				try {
					const cartId = checkbox.dataset.id;
					const response = await fetch(`/cart/${cartId}/select`, {
						method: 'PATCH',
						headers: {
							'Content-Type': 'application/json',
							'Accept': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
						},
						body: JSON.stringify({
							is_selected: isSelected
						})
					});

					if (!response.ok) {
						success = false;
						break;
					}
				} catch (error) {
					console.error('Error:', error);
					success = false;
					break;
				}
			}

			// Update UI based on success
			if (success) {
				checkboxes.forEach(cb => cb.checked = isSelected);
			} else {
				this.checked = !isSelected;
				alert('Failed to update some items. Please try again.');
			}
		});
	}

	// Handle quantity form submissions
	document.querySelectorAll('.quantity-form').forEach(form => {
		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			const cartId = form.dataset.cartId;
			
			try {
				const response = await fetch(form.action, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'Accept': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
					},
					body: JSON.stringify({
						action: form.querySelector('input[name="action"]').value
					})
				});

				if (!response.ok) throw new Error('Network response was not ok');
				
				const data = await response.json();
				if (data.status === 'success') {
					// Update quantity display
					const quantityDisplay = form.parentElement.querySelector('.quantity-display');
					if (quantityDisplay) {
						quantityDisplay.textContent = data.quantity;
					}
				}
			} catch (error) {
				console.error('Error:', error);
			}
		});
	});

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
			// update UI only; quantity will be sent on checkout
			input.value = v;
			// ensure other listeners react (e.g., input event handlers)
			try { input.dispatchEvent(new Event('input', { bubbles: true })); } catch(e) { /* ignore */ }
			calculateSubtotal();
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
			try { input.dispatchEvent(new Event('input', { bubbles: true })); } catch(e) {}
			calculateSubtotal();
		});
	});

	// Listen for manual changes to qty inputs and recalculate
	document.querySelectorAll('.qty').forEach(input => {
		input.addEventListener('input', function(){
			let v = parseInt(this.value || '0', 10);
			if (isNaN(v) || v < 1) v = 1;
			this.value = v;
			calculateSubtotal();
		});
	});

	// Handle checkout form submission: collect selected items and quantities from the frontend
	const checkoutForm = document.querySelector('form[action*="checkout"]');
	if(checkoutForm){
		checkoutForm.addEventListener('submit', function(e){
			const selectedItems = [];
			document.querySelectorAll('.cart-checkbox:checked').forEach(cb => {
				const id = cb.dataset.id;
				const qtyEl = document.querySelector(`.qty[data-id="${id}"]`);
				const qty = qtyEl ? parseInt(qtyEl.value || '0', 10) : 0;
				selectedItems.push({ id: id, quantity: qty });
			});
			if(selectedItems.length === 0){
				e.preventDefault();
				alert('Please select at least one item to checkout');
				return;
			}
			// Put JSON payload in hidden input so server can use frontend quantities
			document.getElementById('selected_items').value = JSON.stringify(selectedItems);
		});
	}

	// Initial subtotal
	calculateSubtotal();
});
</script>
@endpush

</x-app-layout>
