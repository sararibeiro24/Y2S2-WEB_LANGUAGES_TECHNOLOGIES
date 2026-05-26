document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('register-form');
    
    const nameInput = document.getElementById('name-input');

    const usernameInput = document.getElementById('username-input');
    const usernameStatus = document.getElementById('username-status');

    const emailInput = document.getElementById('email-input');
    const emailStatus = document.getElementById('email-status');
    
    const passwordInput = document.getElementById('password-input');
    const confirmPasswordInput = document.getElementById('confirm-password-input');
    const passwordStatus = document.getElementById('password-status1');
    const confirmPasswordStatus = document.getElementById('password-status2');

    let debounceTimeout;

    const formValidity = {
        name: false,
        username: false,
        email: false,
        password: false
    };

    if (!usernameInput || !usernameStatus) return;


    usernameInput.addEventListener('input', () => {
        const username = usernameInput.value.trim();

        clearTimeout(debounceTimeout);
        usernameStatus.textContent = '';
        usernameStatus.className = 'status-message';
        usernameInput.classList.remove('error', 'success');
        formValidity.username = false;

        if (username.length < 3) {
            if (username.length > 0) {
                usernameStatus.textContent = 'Too short (min 3 chars).';
                usernameStatus.classList.add('error');
                usernameInput.classList.add('error');
            }
            return;
        }

        debounceTimeout = setTimeout(() => {
            usernameStatus.textContent = 'Checking...';
            usernameStatus.classList.add('checking');

            fetch(`../actions/action_checkUser.php?username=${encodeURIComponent(username)}`)
                .then(response => response.json())
                .then(data => {
                    usernameStatus.textContent = data.message;
                    usernameStatus.className = 'status-message'; 

                    if (data.available) {
                        usernameStatus.classList.add('success');
                        usernameInput.classList.remove('error');
                        usernameInput.classList.add('success'); 
                        formValidity.username = true; 
                    } else {
                        usernameStatus.classList.add('error');
                        usernameInput.classList.remove('success');
                        usernameInput.classList.add('error'); 
                        formValidity.username = false;
                    }
                })
                .catch(error => {
                    console.error('Error checking username:', error);
                    usernameStatus.textContent = 'Error checking availability.';
                    formValidity.username = false;
                });
        }, 300);
    });

    if (emailInput && emailStatus) {
        emailInput.addEventListener('input', () => {
            const email = emailInput.value.trim();
            emailStatus.textContent = '';
            emailStatus.className = 'status-message';
            emailInput.classList.remove('error', 'success');
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email === '') {
                formValidity.email = false;
                return;
            }

            if (emailRegex.test(email)) {
                emailStatus.textContent = 'Valid email format.';
                emailStatus.classList.add('success');
                emailInput.classList.add('success');
                formValidity.email = true;
            } else {
                emailStatus.textContent = 'Invalid email format.';
                emailStatus.classList.add('error');
                emailInput.classList.add('error');
                formValidity.email = false;
            }
        });
    }
    
    function validatePasswords() {
        if (!passwordInput || !confirmPasswordInput || !passwordStatus || !confirmPasswordStatus) return;

        const pass = passwordInput.value;
        const confirmPass = confirmPasswordInput.value;

        passwordStatus.textContent = '';
        passwordStatus.className = 'status-message';
        
        confirmPasswordStatus.textContent = '';
        confirmPasswordStatus.className = 'status-message';
        
        passwordInput.classList.remove('error', 'success');
        confirmPasswordInput.classList.remove('error', 'success');
        
        formValidity.password = false;

        if (pass === '' && confirmPass === '') return;

        if (pass.length < 6) {
            passwordStatus.textContent = 'Password must be at least 6 characters.';
            passwordStatus.classList.add('error');
            passwordInput.classList.add('error');
            return; 
        }

        passwordInput.classList.add('success');

        if (confirmPass === '') {
            return;
        }

        if (pass === confirmPass) {
            confirmPasswordStatus.textContent = 'Passwords match.';
            confirmPasswordStatus.classList.add('success');
            
            confirmPasswordInput.classList.remove('error');
            confirmPasswordInput.classList.add('success');
            
            formValidity.password = true;
        } else {
            confirmPasswordStatus.textContent = 'Passwords do not match.';
            confirmPasswordStatus.classList.add('error');
            
            confirmPasswordInput.classList.remove('success');
            confirmPasswordInput.classList.add('error');
            
            formValidity.password = false;
        }
    }

    if (passwordInput && confirmPasswordInput) {
        passwordInput.addEventListener('input', validatePasswords);
        confirmPasswordInput.addEventListener('input', validatePasswords);
    }

   if (nameInput) {
    nameInput.addEventListener('input', () => {
        const name = nameInput.value.trim();
        nameInput.classList.remove('error', 'success');

        if (name.length === 0) {
            nameInput.classList.add('error');
            formValidity.name = false; 
        } else {
  
            nameInput.classList.add('success');
            formValidity.name = true;
        }
    });
}


    if (form) {
        form.addEventListener('submit', (event) => {
            if (!formValidity.username || !formValidity.email || !formValidity.password || !formValidity.name) {
                event.preventDefault(); 
                alert('Please fix the errors in the form before submitting.');
            }
        });
    }
});