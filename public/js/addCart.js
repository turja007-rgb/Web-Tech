document.addEventListener('DOMContentLoaded', () => {
    // toast.js contain (showToast) function

    // Attach event listeners to all add-to-cart forms
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (result.success) {
                let message = 'Product added to cart!';
                if (result.addedQty < (parseInt(formData.get('quantity')) || 1)) {
                    message = `Quantity adjusted to ${result.addedQty} due to stock limit.`;
                    showToast(message,'warning');
                    return;

                }
                showToast(message, 'success');

                // Update cart counter
                const counter = document.getElementById('cart-count');
                if (counter && result.cartCount !== undefined) {
                    counter.textContent = result.cartCount;
                }
            } else {
                showToast(result.message || 'Failed to add to cart.', 'danger');
            }
        });
    });
});
