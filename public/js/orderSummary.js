// When Click On View Details Button On profile-> Order History-> View Details

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.view-order-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const orderId = btn.dataset.orderId;
            const url = btn.dataset.summaryUrl;
            const modalContent = document.getElementById('orderDetailsContent');

            modalContent.innerHTML = '<p class="text-center text-muted">Loading...</p>';

            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${encodeURIComponent(orderId)}`,
                credentials: 'same-origin'
            });

            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

            const html = await res.text();

            if (!html.trim()) {
                showToast("No order details found!", "warning");
                modalContent.innerHTML = `<p class="text-muted text-center">No details available.</p>`;
                return;
            }

            modalContent.innerHTML = html;
            new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
        });
    });
});
