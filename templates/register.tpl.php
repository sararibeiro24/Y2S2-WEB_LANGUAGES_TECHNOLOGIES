<?php
function drawRegisterForm() {   
    ?>
    <div class="register-layout">
    <section class="card register">
                <h3>Sign Up</h3>
                <p class="registerSubtitle">It's quick and easy.</p>
                
                <form id="register-form" action="../actions/action_register.php" method="POST">
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="name-input" name="Name" class="input-field" placeholder="e.g., John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text"  id="username-input" name="username" class="input-field" placeholder="e.g., johndoe_99" required>
                        <span id="username-status" class="status-message"></span>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email"  id="email-input" name="email" class="input-field" placeholder="e.g., johndoe@example.com" required>
                        <span id="email-status" class="status-message"></span>
                    </div>  
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password"  id="password-input" name="password" class="input-field" required>
                        <span id="password-status1" class="status-message"></span>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password"  id="confirm-password-input" name="confirmPassword" class="input-field" required>
                        <span id="password-status2" class="status-message"></span>
                    </div>
                    <button type="submit" class="button">Register</button>
                </form>
            </section>
    </div>
<?php
}

