document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('register-form')) {
        initRegisterValidation();
    }
    
    if (document.getElementById('profile-form')) {
        initProfileValidation();
    }
});


const formValidityStates = {
    name: false,
    username: false,
    email: false,
    password: false
};

let debounceTimeout;

function validateNameField(inputElement) {
    if (!inputElement) return false;
    const name = inputElement.value.trim();
    
    inputElement.classList.remove('error', 'success');
    
    if (name.length === 0) {
        inputElement.classList.add('error');
        formValidityStates.name = false;
        return false;
    } else {
        inputElement.classList.add('success');
        formValidityStates.name = true;
        return true;
    }
}

function validateEmailField(inputElement, statusElement) {
    if (!inputElement || !statusElement) return false;

    const email = inputElement.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Reset styles
    statusElement.textContent = '';
    statusElement.className = 'status-message';
    inputElement.classList.remove('error', 'success');

    if (email === '') {
        formValidityStates.email = false;
        return false;
    }

    if (emailRegex.test(email)) {
        statusElement.textContent = 'Valid email format.';
        statusElement.classList.add('success');
        inputElement.classList.add('success');
        formValidityStates.email = true;
        return true;
    } else {
        statusElement.textContent = 'Invalid email format.';
        statusElement.classList.add('error');
        inputElement.classList.add('error');
        formValidityStates.email = false;
        return false;
    }
}

function validatePasswordMatching(passInput, confirmInput, status1, status2, isRequired = true) {
    if (!passInput || !confirmInput || !status1 || !status2) return false;

    const pass = passInput.value;
    const confirmPass = confirmInput.value;

    status1.textContent = ''; status1.className = 'status-message';
    status2.textContent = ''; status2.className = 'status-message';
    passInput.classList.remove('error', 'success');
    confirmInput.classList.remove('error', 'success');

    if (!isRequired && pass === '' && confirmPass === '') {
        formValidityStates.password = true;
        return true;
    }

    if (pass.length < 6) {
        status1.textContent = 'Password must be at least 6 characters.';
        status1.classList.add('error');
        passInput.classList.add('error');
        formValidityStates.password = false;
        return false;
    }
    passInput.classList.add('success');

    if (confirmPass === '') {
        formValidityStates.password = false;
        return false;
    }

    if (pass === confirmPass) {
        status2.textContent = 'Passwords match.';
        status2.classList.add('success');
        confirmInput.classList.add('success');
        formValidityStates.password = true;
        return true;
    } else {
        status2.textContent = 'Passwords do not match.';
        status2.classList.add('error');
        confirmInput.classList.add('error');
        formValidityStates.password = false;
        return false;
    }
}

function checkUsernameAvailabilityLive(inputElement, statusElement) {
    if (!inputElement || !statusElement) return;

    const username = inputElement.value.trim();

    clearTimeout(debounceTimeout);
    statusElement.textContent = '';
    statusElement.className = 'status-message';
    inputElement.classList.remove('error', 'success');
    formValidityStates.username = false;

    if (username.length < 3) {
        if (username.length > 0) {
            statusElement.textContent = 'Too short (min 3 chars).';
            statusElement.classList.add('error');
            inputElement.classList.add('error');
        }
        return;
    }

    debounceTimeout = setTimeout(() => {
        statusElement.textContent = 'Checking...';
        statusElement.className = 'status-message checking';

        fetch(`../actions/action_checkUser.php?username=${encodeURIComponent(username)}`)
            .then(response => response.json())
            .then(data => {
                statusElement.textContent = data.message;
                statusElement.className = 'status-message'; 

                if (data.available) {
                    statusElement.classList.add('success');
                    inputElement.classList.add('success'); 
                    formValidityStates.username = true; 
                } else {
                    statusElement.classList.add('error');
                    inputElement.classList.add('error'); 
                    formValidityStates.username = false;
                }
            })
            .catch(error => {
                console.error('Error checking username:', error);
                statusElement.textContent = 'Error checking availability.';
                statusElement.className = 'status-message error';
                formValidityStates.username = false;
            });
    }, 300);
}


function initRegisterValidation() {
    const form = document.getElementById('register-form');
    const nameInput = document.getElementById('name-input');
    const usernameInput = document.getElementById('username-input');
    const usernameStatus = document.getElementById('username-status');
    const emailInput = document.getElementById('email-input');
    const emailStatus = document.getElementById('email-status');
    const passInput = document.getElementById('password-input');
    const confirmInput = document.getElementById('confirm-password-input');
    const status1 = document.getElementById('password-status1');
    const status2 = document.getElementById('password-status2');

    if (nameInput) {
        nameInput.addEventListener('input', () => validateNameField(nameInput));
    }
    if (usernameInput && usernameStatus) {
        usernameInput.addEventListener('input', () => checkUsernameAvailabilityLive(usernameInput, usernameStatus));
    }
    if (emailInput && emailStatus) {
        emailInput.addEventListener('input', () => validateEmailField(emailInput, emailStatus));
    }
    if (passInput && confirmInput) {
        passInput.addEventListener('input', () => validatePasswordMatching(passInput, confirmInput, status1, status2, true));
        confirmInput.addEventListener('input', () => validatePasswordMatching(passInput, confirmInput, status1, status2, true));
    }

    if (form) {
        form.addEventListener('submit', (event) => {
            if (!formValidityStates.username || !formValidityStates.email || !formValidityStates.password || !formValidityStates.name) {
                event.preventDefault(); 
                alert('Please fix the errors in the form before submitting.');
            }
        });
    }
}

function initProfileValidation() {
    const form = document.getElementById('profile-form');
    const emailInput = document.getElementById('profile-email-input');
    const emailStatus = document.getElementById('profile-email-status');
    const passInput = document.getElementById('profile-password-input');
    const confirmInput = document.getElementById('profile-confirm-password-input');
    const status1 = document.getElementById('profile-password-status1');
    const status2 = document.getElementById('profile-password-status2');

    formValidityStates.email = true;
    formValidityStates.password = true;
    formValidityStates.name = true;
    formValidityStates.username = true;

    if (emailInput && emailStatus) {
        emailInput.addEventListener('input', () => validateEmailField(emailInput, emailStatus));
    }
    if (passInput && confirmInput) {
        passInput.addEventListener('input', () => validatePasswordMatching(passInput, confirmInput, status1, status2, false));
        confirmInput.addEventListener('input', () => validatePasswordMatching(passInput, confirmInput, status1, status2, false));
    }

    if (form) {
        form.addEventListener('submit', (event) => {
            if (!formValidityStates.email || !formValidityStates.password) {
                event.preventDefault();
                alert('Please correct your profile changes before saving.');
            }
        });
    }
}
