<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();

// If already logged in, redirect to profile
if (Session::isLoggedIn()) {
    header('Location: profile.php');
    exit;
}

drawHead("Login | Ladybug's Gym");
drawHeader();

$messages = Session::getMessages();
?>
<!DOCTYPE html>
<html lang="en">

<body>

    <main class="container login-page">
        
        <div class="page-header">
            <h2>Welcome Back</h2>
            <p>Please log in to access your account.</p>
        </div>

        <?php if (!empty($messages)): ?>
            <div class="messages">
                <?php foreach ($messages as $msg): ?>
                    <div class="message message-<?php echo htmlspecialchars($msg['type']); ?>">
                        <p><?php echo htmlspecialchars($msg['text']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="login-layout">
            
            <section class="card login-card">
                <h3>Member & Trainer</h3>
                <p class="login-subtitle">Access your schedule and profile.</p>
                
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
                    
                    <p class="login-footer">Don't have an account? <a href="#">Register here</a></p>
                </form>
            </section>

            <section class="card login-card admin-login">
                <h3>System Admin</h3>
                <p class="login-subtitle">Manage gym operations.</p>
                
                <form action="../actions/action_login.php" method="POST">
                    <input type="hidden" name="login_type" value="admin">
                    
                    <div class="form-group">
                        <label for="adminId">Admin ID</label>
                        <input type="text" id="adminId" name="adminId" class="input-field" placeholder="Admin Identification Number" required>
                    </div>

                    <div class="form-group">
                        <label for="adminPassword">Admin Password</label>
                        <input type="password" id="adminPassword" name="password" class="input-field" required>
                    </div>

                    <div class="form-group">
                        <label for="adminToken">Security Token</label>
                        <input type="password" id="adminToken" name="token" class="input-field" placeholder="2FA Token" required>
                    </div>

                    <button type="submit" class="button button-full button-outline">Admin Access</button>
                </form>
            </section>

        </div>
    </main>


</body>
</html>
<?php
drawFooter();
?>