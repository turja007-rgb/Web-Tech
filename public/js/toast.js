/*function showToast(message, type = "danger") {
    const toastContainer = document.getElementById("cart-feedback");

    if (!toastContainer) {
        console.error("Toast container not found!");
        return;
    }

    const toast = document.createElement("div");
    toast.className = `toast align-items-center text-bg-${type} border-0 mb-2`;
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();

    // remove after hidden
    toast.addEventListener("hidden.bs.toast", () => toast.remove());
}*/
function showToast(message, type = "danger", position = "corner") {
    // Decide which container to use
    const containerId = (position === "center") ? "toast-center" : "cart-feedback";
    const toastContainer = document.getElementById(containerId);

    if (!toastContainer) {
        console.error(`Toast container "${containerId}" not found!`);
        return;
    }

    const toast = document.createElement("div");
    toast.className = `toast align-items-center text-bg-${type} border-0 mb-2`;
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body ${position === "center" ? "fs-4 fw-semibold p-3" : ""}">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();

    // Remove after hidden
    toast.addEventListener("hidden.bs.toast", () => toast.remove());
}

function flashToast(message, type = "danger", position = "corner") {
    if (document.readyState === "loading") {
        window.addEventListener("DOMContentLoaded", function() {
            showToast(message, type, position);
        });
    } else {
        showToast(message, type, position);
    }
}

function flashToast(message, type = 'danger') {
    if (document.readyState === "loading") {
        // Page still loading → wait for DOMContentLoaded
        window.addEventListener("DOMContentLoaded", function() {
            showToast(message, type);
        });
    } else {
        // DOM already loaded → show immediately
        showToast(message, type);
    }
}

