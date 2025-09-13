document.querySelectorAll('.profile-field').forEach(field => {
    const displayDiv = field.querySelector('.field-display');
    const input = field.querySelector('.editable-input');
    const btn = field.querySelector('.edit-btn');

    // Edit click → show empty input
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        displayDiv.classList.add('d-none');
        input.classList.remove('d-none');
        input.value = ''; // clear input on edit
        input.focus();
    });

    // Click outside → revert to plain text
    document.addEventListener('click', (e) => {
        if (!field.contains(e.target)) {
            if (!displayDiv.classList.contains('d-none')) return;
            displayDiv.classList.remove('d-none');
            input.classList.add('d-none');
        }
    });
});