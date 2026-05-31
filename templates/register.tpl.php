<?php
function drawRegisterForm() {
    ?>
    <main class="register-page">
        <div class="register-layout">
            <div class="register-card">
                <h3>Create Your Account</h3>
                <p class="register-subtitle">Join the hive and start your journey</p>

                <form id="register-form" action="../actions/action_register.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                    <div class="form-group">
                        <label for="name-input">Full Name</label>
                        <input type="text" id="name-input" name="name" class="input-field" placeholder="e.g., John Doe" required>
                    </div>

                    <div class="form-group">
                        <label for="username-input">Username</label>
                        <input type="text" id="username-input" name="username" class="input-field" placeholder="e.g., johndoe_99" required>
                        <span id="username-status" class="status-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="email-input">Email Address</label>
                        <input type="email" id="email-input" name="email" class="input-field" placeholder="e.g., johndoe@example.com" required>
                        <span id="email-status" class="status-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="password-input">Password</label>
                        <input type="password" id="password-input" name="password" class="input-field" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
                        <span id="password-status1" class="status-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password-input">Confirm Password</label>
                        <input type="password" id="confirm-password-input" name="confirmPassword" class="input-field" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
                        <span id="password-status2" class="status-message"></span>
                    </div>

                    <button type="submit" class="button">Create Account</button>

                    <p class="register-footer">Already have an account? <a href="login.php">Sign in</a></p>
                </form>
            </div>
        </div>
    </main>
<?php
}
