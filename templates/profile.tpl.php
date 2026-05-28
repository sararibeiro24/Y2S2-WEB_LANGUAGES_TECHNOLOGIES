<?php
function drawProfileForm($Name, $Username, $Email, $ProfilePhoto, $badge, $memberSince, $classesAttended, $upcomingClasses, $Role = 'member') {
    $date = new DateTime($memberSince);
    $formattedDate = $date->format('M Y');
    $badgeClass ='';
    if ($badge) {
        $lowerBadge = strtolower($badge);
        if (strpos($lowerBadge, 'elite') !== false || strpos($lowerBadge, 'annual') !== false) {
            $badgeClass = 'badge-yellow';
        } elseif (strpos($lowerBadge, 'premium') !== false || strpos($lowerBadge, 'pro') !== false) {
            $badgeClass = 'badge-green';
        }
    }
?>
    <main class="container profile-page">   
        
        <div class="page-header">
            <h2>Account Settings</h2>
            <p>Update your personal information and security settings.</p>
        </div>

        <div class="profile-layout">
            
            <aside class="profile-sidebar card">
                <img src="<?= htmlspecialchars($ProfilePhoto) ?>" alt="Current Profile Photo" class="profile-avatar" loading="lazy">
                <h3><?= htmlspecialchars($Username) ?></h3>
                
                <?php if ($Role === 'trainer' ): ?>
                    
                    <span class="badge badge-red">Trainer</span>
                    <p class="member-since">Trainer since <?= htmlspecialchars($formattedDate) ?></p>
                    
                <?php elseif ($Role === 'member'): ?>
                    
                    <?php if ($badge): ?>
                        <span class="badge <?= htmlspecialchars($badgeClass) ?>"><?= htmlspecialchars($badge) ?></span>
                    <?php else: ?>
                        <span class="badge badge-gray">No Active Plan</span>
                    <?php endif; ?>
                    
                    <p class="member-since">Member since <?= htmlspecialchars($formattedDate) ?></p>
                    
                    <ul class="profile-stats">
                        <li><span>Classes Attended</span> <strong><?= htmlspecialchars((string)$classesAttended) ?></strong></li>
                        <li><span>Upcoming</span> <strong><?= htmlspecialchars((string)$upcomingClasses) ?></strong></li>
                    </ul>
                <?php else: ?>
                    <span class="badge badge-red">Admin</span>
                    <p class="member-since">Admin since <?= htmlspecialchars($formattedDate) ?></p>
                <?php endif; ?>
            </aside>

            <div class="profile-form-container card">
                <form id="profile-form" action="../actions/action_editProfile.php" method="POST" enctype="multipart/form-data">
                    
                    <fieldset class="form-section">
                        <legend>Personal Information</legend>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullName" name="fullName" class="input-field" value="<?= htmlspecialchars($Name) ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" class="input-field" value="<?= htmlspecialchars($Username) ?>" disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="profile-email-input">Email Address</label>
                            <input type="email" id="profile-email-input" name="email" class="input-field" value="<?= htmlspecialchars($Email) ?>" required>
                            <span id="profile-email-status" class="status-message"></span>
                        </div>
                    </fieldset>

                    <fieldset class="form-section">
                        <legend>Profile Photo</legend>
                        <div class="form-group">
                            <label for="profile-photo-input">Upload New Photo</label>
                            <input type="file" id="profile-photo-input" name="profile_photo" class="input-field" accept="image/png, image/jpeg, image/webp">
                            <span id="profile-photo-status" class="status-message"></span>
                        </div>
                    </fieldset>

                    <fieldset class="form-section">
                        <legend>Security</legend>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="profile-password-input">New Password</label>
                                <input type="password" id="profile-password-input" name="password" class="input-field" placeholder="Leave blank to keep current">
                                <span id="profile-password-status1" class="status-message"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="profile-confirm-password-input">Confirm Password</label>
                                <input type="password" id="profile-confirm-password-input" name="confirmPassword" class="input-field" placeholder="Confirm new password">
                                <span id="profile-password-status2" class="status-message"></span>
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-actions">
                        <button type="button" class="button button-outline" onclick="window.location.href='index.php'">Cancel</button>
                        <button type="submit" class="button">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
<?php
}
?>