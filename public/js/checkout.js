document.addEventListener('DOMContentLoaded', () => {
    const deliverySelect = document.getElementById('delivery_option');
    const deliveryWrap = document.getElementById('delivery_address_wrap');
    const subtotalEl = document.getElementById('subtotal');
    const deliveryEl = document.getElementById('delivery_fee');
    const totalEl = document.getElementById('total');

    const deliveryUrl = deliverySelect.dataset.deliveryUrl;

    deliverySelect.addEventListener('change', async function() {
        if (this.value === 'delivery') {
            deliveryWrap.style.display = 'block';

            // Fetch updated totals from server
            const res = await fetch(deliveryUrl, {
                method: 'POST',
                credentials: 'same-origin'
            });
            const data = await res.json();
            //If fetch Successfully
            if (data.success) {
                subtotalEl.textContent = data.subtotal.toFixed(2);
                deliveryEl.textContent = data.deliveryFee.toFixed(2);
                totalEl.textContent = data.total.toFixed(2);
            }

        } else {
            deliveryWrap.style.display = 'none';
            deliveryEl.textContent = '0.00';
            totalEl.textContent = subtotalEl.textContent;
        }
    });
});
