document.addEventListener('DOMContentLoaded', () => {
    const showToast = window.showToast;

    const updateCartTotals = (subtotal) => {
        const subtotalEl = document.getElementById('subtotal');
        if (subtotalEl) subtotalEl.textContent = subtotal.toFixed(2);
    };

    // Increment / Decrement buttons
    document.querySelectorAll('.increment-btn, .decrement-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-group').querySelector('.quantity-input');
            let value = parseInt(input.value) || 1;

            if (btn.classList.contains('increment-btn')) {
                value = Math.min(value + 1, parseInt(input.max));
            } else {
                value = Math.max(value - 1, parseInt(input.min));
            }

            input.value = value;

            // Update line total (numeric only)
            const row = input.closest('tr');
            const price = parseFloat(row.querySelector('td:nth-child(3)').textContent.replace(/[^\d.]/g, '')) || 0;
            row.querySelector('.line-total').textContent = (price * value).toFixed(2);

            // Recalculate subtotal
            let subtotal = 0;
            document.querySelectorAll('.line-total').forEach(span => {
                subtotal += parseFloat(span.textContent) || 0;
            });
            updateCartTotals(subtotal);
        });
    });

    // Update quantity button
    document.querySelectorAll('.update-item-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const productId = btn.dataset.productId;
            const url = btn.dataset.productUrl;
            const qtyInput = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            if (!qtyInput) return;

            let qty = parseInt(qtyInput.value) || 1;
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', qty);

            try {
                const res = await fetch(url, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    qtyInput.value = data.finalQty;
                    showToast(data.message, 'success');

                    const row = qtyInput.closest('tr');
                    const price = parseFloat(row.querySelector('td:nth-child(3)').textContent.replace(/[^\d.]/g, '')) || 0;
                    row.querySelector('.line-total').textContent = (price * data.finalQty).toFixed(2);

                    // Update subtotal
                    let subtotal = 0;
                    document.querySelectorAll('.line-total').forEach(span => {
                        subtotal += parseFloat(span.textContent) || 0;
                    });
                    updateCartTotals(subtotal);
                } else {
                    showToast(data.message || 'Update failed', 'danger');
                }
            } catch (err) {
                showToast('Request failed', 'danger');
                console.error(err);
            }
        });
    });

    // Remove item
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const productId = btn.dataset.productId;
            const url = btn.dataset.productUrl;
            if (!url) return;

            const formData = new FormData();
            formData.append('product_id', productId);

            try {
                const res = await fetch(url, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message, 'success');

                    const row = btn.closest('tr');
                    row.remove();

                    // Update subtotal
                    let subtotal = 0;
                    document.querySelectorAll('.line-total').forEach(span => {
                        subtotal += parseFloat(span.textContent) || 0;
                    });
                    updateCartTotals(subtotal);

                    if (!document.querySelector('tbody tr')) {
                        location.reload();
                    }
                } else {
                    showToast(data.message || 'Remove failed', 'danger');
                }
            } catch (err) {
                showToast('Request failed', 'danger');
                console.error(err);
            }
        });
    });
});
