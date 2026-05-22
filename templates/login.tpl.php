<?php
function drawLoginForm(){
?>
    <main class="container login-page">
   <div class="login-layout">
            
            <section class="card login-card">
                
                <form action="../actions/action_login.php" method="POST">
                    <input type="hidden" name="login_type" value="user">
                    
                    <div class="form-group">
                        <label for="userUsername">Username or Email</label>
                        <input type="text" id="userUsername" name="username" class="input-field" placeholder="e.g., johndoe_99" required>
                    </div>

                    <div class="form-group">
                        <label for="userPassword">Password</label>
                        <input type="password" id="userPassword" name="password" class="input-field" required>
                    </div>

                    <button type="submit" class="button button-full">Log In</button>
                    
                    <p class="login-footer">Don't have an account? <a href="register.php">Register here </a></p>
                </form>
            </section>
        </div>
</main>
<?php
}