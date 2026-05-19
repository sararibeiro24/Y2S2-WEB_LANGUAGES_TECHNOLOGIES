<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Ladybug's Gym</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/components.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/pages.css">
</head>
<body>

    <header class="site-header">
        <div class="container header-container">
            <h1>Ladybug's Gym</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="schedule.php">Classes</a></li>
                    <li><a href="#">Trainers</a></li>
                    <li><a href="login.php" class="button button-small active">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container login-page">
        
        <div class="page-header">
            <h2>Welcome Back</h2>
            <p>Please log in to access your account.</p>
        </div>

        <div class="login-layout">
            
            <section class="card login-card">
                <h3>Member & Trainer</h3>
                <p class="login-subtitle">Access your schedule and profile.</p>
                
                <form action="profile.php" method="GET">
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
                
                <form action="profile.php" method="GET">
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

    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Ladybug's Gym</p>
        </div>
    </footer>

</body>
</html>
