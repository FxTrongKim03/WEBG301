// Student Management System - JavaScript Enhancements

console.log('Student Management System loaded');

// Form validation enhancement
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Add loading state to forms on submit
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !form.classList.contains('no-loading')) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                
                // Re-enable if validation fails
                setTimeout(() => {
                    if (!form.checkValidity()) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }, 100);
            }
        });
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const confirmMessage = button.dataset.confirmDelete || 'Are you sure you want to delete this item?';
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Table row click to view details
    const tableRows = document.querySelectorAll('table[data-click-row] tbody tr');
    tableRows.forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on a button or link
            if (!e.target.closest('a, button')) {
                const link = row.querySelector('a[data-row-link]');
                if (link) {
                    window.location.href = link.href;
                }
            }
        });
    });

    // Search filter for tables
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(input => {
        const tableId = input.dataset.tableSearch;
        const table = document.getElementById(tableId);
        if (!table) return;

        input.addEventListener('keyup', function() {
            const filter = input.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });

    // Add tooltip initialization if Bootstrap tooltips are used
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Character counter for textareas
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const counter = document.createElement('small');
        counter.className = 'text-muted d-block mt-1';
        counter.textContent = `0 / ${maxLength} characters`;
        textarea.parentNode.appendChild(counter);

        textarea.addEventListener('input', function() {
            const currentLength = textarea.value.length;
            counter.textContent = `${currentLength} / ${maxLength} characters`;
            counter.className = currentLength > maxLength * 0.9 
                ? 'text-warning d-block mt-1' 
                : 'text-muted d-block mt-1';
        });
    });
});

// Utility function: Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(toast);
        bsAlert.close();
    }, 3000);
}

// Export for use in inline scripts if needed
window.showToast = showToast;

// AJAX form handler (for .ajax-form) and undo handling for toast inline forms
document.addEventListener('DOMContentLoaded', function() {
    // Handle AJAX forms
    document.querySelectorAll('form.ajax-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type=submit]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const original = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            }

            try {
                const action = form.getAttribute('action') || window.location.href;
                const options = {
                    method: (form.getAttribute('method') || 'POST').toUpperCase(),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form)
                };

                const resp = await fetch(action, options);
                const data = await resp.json();
                if (data.status === 'success') {
                    // close any parent offcanvas
                    const off = form.closest('.offcanvas');
                    if (off) {
                        const instance = bootstrap.Offcanvas.getInstance(off);
                        if (instance) instance.hide();
                    }
                    // show server message as toast
                    if (data.message) showToastHtml(data.message, 'success');
                } else {
                    const msg = data.message || 'An error occurred';
                    showToastHtml(msg, 'danger');
                }
            } catch (err) {
                showToastHtml('Network error', 'danger');
                console.error(err);
            }

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.getAttribute('data-original') || submitBtn.innerHTML;
            }
        });
    });

    // Delegate submit handling for undo forms inside toast container
    const toastContainer = document.getElementById('toastContainer');
    if (toastContainer) {
        toastContainer.addEventListener('submit', async function(e) {
            const form = e.target;
            if (form && form.classList.contains('undo-form')) {
                e.preventDefault();
                try {
                    const action = form.getAttribute('action');
                    const resp = await fetch(action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(form) });
                    const data = await resp.json().catch(() => ({ status: 'ok' }));
                    // Remove parent toast
                    const toastEl = form.closest('.toast');
                    if (toastEl) {
                        const bs = bootstrap.Toast.getInstance(toastEl) || new bootstrap.Toast(toastEl);
                        bs.hide();
                    }
                    showToastHtml('Action undone', 'info');
                } catch (err) {
                    showToastHtml('Unable to undo action', 'danger');
                }
            }
        });
    }

    // Helper to inject toast HTML and display it using Bootstrap Toast
    function showToastHtml(html, type='info') {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const visual = type === 'danger' ? 'danger' : (type === 'success' ? 'success' : 'info');
        const wrapper = document.createElement('div');
        wrapper.className = `toast align-items-center text-bg-${visual} border-0 mb-2`;
        wrapper.setAttribute('role','alert');
        wrapper.setAttribute('aria-live','assertive');
        wrapper.setAttribute('aria-atomic','true');
        wrapper.innerHTML = `<div class="d-flex"><div class="toast-body">${html}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
        container.appendChild(wrapper);
        const bsToast = new bootstrap.Toast(wrapper, { delay: 6000 });
        bsToast.show();
    }

    // API UI removed: create/load/delete via API are disabled in the frontend for anonymous users.
});
