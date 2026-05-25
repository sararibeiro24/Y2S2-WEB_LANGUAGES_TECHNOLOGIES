document.addEventListener('DOMContentLoaded', () => {
    const usernameInput = document.getElementById('username-input');
    const usernameStatus = document.getElementById('username-status');
    let debounceTimeout;

    if (!usernameInput || !usernameStatus) return;

    usernameInput.addEventListener('input', () => {
        const username = usernameInput.value.trim();

        clearTimeout(debounceTimeout);
        usernameStatus.textContent = '';
        usernameStatus.className = 'status-message';

        if (username.length < 3) {
            if (username.length > 0) {
                usernameStatus.textContent = 'Too short (min 3 chars).';
                usernameStatus.classList.add('error');
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
                        usernameInput.style.borderColor = '#137333'; 
                        
                    } else {
                        usernameStatus.classList.add('error');
                        usernameInput.style.borderColor = '#a94442'; 
                        
                    }
                })
                .catch(error => {
                    console.error('Error checking username:', error);
                    usernameStatus.textContent = 'Error checking availability.';
                });
        }, 300);
    });
});