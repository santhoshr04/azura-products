import Swal from 'sweetalert2';

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3400,
    timerProgressBar: true,
});

const confirmDialog = ({ title, text, danger = false, confirmText }) =>
    Swal.fire({
        title,
        text,
        icon: danger ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancel',
        confirmButtonColor: danger ? '#e11d48' : '#4f46e5',
        cancelButtonColor: '#64748b',
        reverseButtons: true,
        focusCancel: danger,
    });

const flash = document.querySelector('[data-flash-notice]');

if (flash) {
    const message = flash.dataset.message;

    flash.remove();

    if (message) {
        toast.fire({
            icon: flash.dataset.type === 'error' ? 'error' : 'success',
            title: message,
        });
    }
}

const rules = {
    required: (value) => value.trim() !== '',
    numeric: (value) => value.trim() !== '' && !Number.isNaN(Number(value)),
    gt: (value, threshold) => value.trim() !== '' && Number(value) > Number(threshold),
};

const ruleMessages = {
    required: (label) => `${label} is required.`,
    numeric: (label) => `${label} must be a valid number.`,
    gt: (label, threshold) =>
        Number(threshold) === 0
            ? `${label} must be greater than 0.`
            : `${label} must be greater than ${threshold}.`,
};

const fieldLabel = (field) => {
    const label = document.querySelector(`label[for="${CSS.escape(field.id)}"]`);

    return label ? label.textContent.replace('*', '').trim() : 'This field';
};

const errorElement = (field) => {
    const id = `${field.id}-error`;

    let error = document.getElementById(id);

    if (!error) {
        const wrapper =
            document.querySelector(`[data-field="${CSS.escape(field.name)}"]`) ?? field.parentElement;

        error = document.createElement('p');
        error.id = id;
        error.className = 'mt-1.5 text-sm text-rose-600';
        wrapper.appendChild(error);
    }

    return error;
};

const validateField = (field) => {
    const failed = (field.dataset.rules ?? '')
        .split('|')
        .filter(Boolean)
        .find((rule) => {
            const [name, threshold] = rule.split(':');

            return name in rules && !rules[name](field.value, threshold);
        });

    if (!failed) {
        field.classList.remove('border-rose-300', 'focus:border-rose-400', 'focus:ring-rose-100');
        field.removeAttribute('aria-invalid');
        field.removeAttribute('aria-describedby');
        document.getElementById(`${field.id}-error`)?.remove();

        return true;
    }

    const [name, threshold] = failed.split(':');
    const error = errorElement(field);

    error.textContent = ruleMessages[name](fieldLabel(field), threshold);

    field.classList.add('border-rose-300', 'focus:border-rose-400', 'focus:ring-rose-100');
    field.setAttribute('aria-invalid', 'true');
    field.setAttribute('aria-describedby', error.id);

    return false;
};

const validateForm = (form) => {
    const invalid = Array.from(form.querySelectorAll('[data-rules]')).filter(
        (field) => !validateField(field),
    );

    if (invalid.length === 0) {
        return true;
    }

    invalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    invalid[0].focus({ preventScroll: true });

    return false;
};

document.querySelectorAll('[data-rules]').forEach((field) => {
    field.addEventListener('blur', () => validateField(field));

    field.addEventListener(field.tagName === 'SELECT' ? 'change' : 'input', () => {
        if (field.getAttribute('aria-invalid') === 'true') {
            validateField(field);
        }
    });
});

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const needsValidation = form.hasAttribute('data-validate');
    const message = form.dataset.confirm;
    const submitTitle = form.dataset.confirmSubmit;
    const needsConfirmation = Boolean(message || submitTitle);

    if (!needsValidation && !needsConfirmation) {
        return;
    }

    event.preventDefault();

    if (needsValidation && !validateForm(form)) {
        toast.fire({ icon: 'error', title: 'Please fix the highlighted fields.' });

        return;
    }

    const submit = () => {
        form.dataset.submitting = 'true';
        form.submit();
    };

    if (!needsConfirmation) {
        submit();

        return;
    }

    const isDelete = form.querySelector('input[name="_method"]')?.value?.toUpperCase() === 'DELETE';

    confirmDialog({
        title: isDelete ? (form.dataset.confirmTitle ?? 'Delete item?') : (submitTitle ?? 'Please confirm'),
        text: message ?? 'Please review the details before continuing.',
        danger: isDelete,
        confirmText: isDelete ? 'Yes, delete it' : 'Yes, continue',
    }).then((result) => {
        if (result.isConfirmed) {
            submit();
        }
    });
});

const trackedForm = document.querySelector('form[data-track-dirty]');

if (trackedForm) {
    let dirty = false;

    const submitInProgress = () => trackedForm.dataset.submitting === 'true';

    trackedForm.addEventListener('input', () => {
        dirty = true;
    });
    trackedForm.addEventListener('change', () => {
        dirty = true;
    });

    window.addEventListener('beforeunload', (event) => {
        if (!dirty || submitInProgress()) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    });

    document.querySelectorAll('[data-confirm-leave]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (!dirty || submitInProgress()) {
                return;
            }

            event.preventDefault();

            confirmDialog({
                title: 'Discard changes?',
                text: 'You have unsaved changes that will be lost.',
                danger: true,
                confirmText: 'Discard changes',
            }).then((result) => {
                if (result.isConfirmed) {
                    dirty = false;
                    window.location.assign(link.href);
                }
            });
        });
    });
}

const filterForm = document.querySelector('form[data-auto-filter]');

if (filterForm) {
    let debounce;
    let composing = false;

    const applyFilters = () => {
        window.clearTimeout(debounce);
        filterForm.submit();
    };

    filterForm.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', applyFilters);
    });

    const searchInput = filterForm.querySelector('input[type="search"]');

    if (searchInput) {
        searchInput.addEventListener('compositionstart', () => {
            composing = true;
        });
        searchInput.addEventListener('compositionend', () => {
            composing = false;
        });

        searchInput.addEventListener('input', () => {
            if (composing) {
                return;
            }

            window.clearTimeout(debounce);
            debounce = window.setTimeout(applyFilters, 400);
        });

        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                applyFilters();
            }
        });
    }
}
