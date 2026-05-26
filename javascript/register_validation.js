document.addEventListener('DOMContentLoaded', () => {
    // Backwards-compatible stub: if an older page expects initRegistrationValidation,
    // it can define that function in `javascript/validation.js` and this will call it.
    if (typeof window.initRegistrationValidation === 'function') {
        window.initRegistrationValidation();
    }
});
