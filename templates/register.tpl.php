<?php
function drawRegisterForm() {   
    ?>
    <div class="register-layout">
    <section class="card register">
                <h3>Sign Up</h3>
                <p class="registerSubtitle">It's quick and easy.</p>
                
                <form action="../actions/action_register.php" method="POST">
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text"  name="Name" class="input-field" placeholder="e.g., John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text"  name="username" class="input-field" placeholder="e.g., johndoe_99" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="input-field" placeholder="e.g., johndoe@example.com" required>
                    </div>  
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="input-field" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirmPassword" class="input-field" required>
                    </div>
                    <button type="submit" class="button">Register</button>
                </form>
            </section>
    </div>
<?php
}

