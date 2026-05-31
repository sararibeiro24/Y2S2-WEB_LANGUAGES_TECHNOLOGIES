<?php
function drawLoginForm(){
?>
    <main class="login-page">
        <div class="login-layout">
            <div class="login-card">
                <h3>Welcome Back</h3>
                <p class="login-subtitle">Sign in to your account</p>

                <form action="../actions/action_login.php" method="POST">
                    <input type="hidden" name="login_type" value="user">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                    <div class="form-group">
                        <label for="userUsername">Username or Email</label>
                        <input type="text" id="userUsername" name="username" class="input-field" placeholder="e.g., johndoe_99" required>
                    </div>

                    <div class="form-group">
                        <label for="userPassword">Password</label>
                        <input type="password" id="userPassword" name="password" class="input-field" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
                    </div>

                    <button type="submit" class="button">Log In</button>

                    <p class="login-footer">Don't have an account? <a href="register.php">Register here</a></p>
                </form>
            </div>
        </div>
    </main>
<?php
}