document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('profileForm');
    if (!form) return;
    form.querySelectorAll('.profile-field').forEach(field => {
        const displayDiv = field.querySelector('.field-display');
        const input = field.querySelector('.editable-input');
        const btn = field.querySelector('.edit-btn');

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            displayDiv.classList.add('d-none');
            input.classList.remove('d-none');
            // Pre-fill input with current value
            input.value = displayDiv.querySelector('span').textContent.trim();
            input.focus();
        });

        document.addEventListener('click', (e) => {
            if (!field.contains(e.target)) {
                displayDiv.classList.remove('d-none');
                input.classList.add('d-none');
            }
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = new FormData(form);

        try {
            const res = await fetch(form.action, { method: 'POST', body: data, credentials: 'same-origin' });
            const result = await res.json();
            console.log(result);

            if (result.status === 'success') {
                showToast(result.message || "Profile updated!", "success");
                // Update displayed values
                Object.keys(result.updatedFields || {}).forEach(key => {
                    const span = form.querySelector(`.profile-field input[name="${key}"]`)?.previousElementSibling.querySelector('span');
                    if (span) span.textContent = result.updatedFields[key];
                });
            } else {
                // Show validation errors
                Object.values(result.errors || {}).forEach(msg => showToast(msg, "danger"));
            }
        } catch (err) {
            console.error(err);
            showToast("Server error!", "danger");
        }
    });
});
