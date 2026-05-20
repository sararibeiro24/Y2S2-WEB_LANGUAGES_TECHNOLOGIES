<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
drawHead("My Profile | Ladybug's Gym");
drawHeader();

?>
<!DOCTYPE html>
<html lang="en">

<body>

    <main class="container profile-page">
        
        <div class="page-header">
            <h2>Account Settings</h2>
            <p>Update your personal information and security settings.</p>
        </div>

        <div class="profile-layout">
            
            <aside class="profile-sidebar card">
                <img src="../img/John_Doe.png" alt="Current Profile Photo" class="profile-avatar">
                <h3>johndoe_99</h3>
                <span class="badge badge-green">Pro Member</span>
                <p class="member-since">Member since Jan 2026</p>
                
                <ul class="profile-stats">
                    <li><span>Classes Attended</span> <strong>42</strong></li>
                    <li><span>Upcoming</span> <strong>3</strong></li>
                </ul>
            </aside>

            <div class="profile-form-container card">
                <form action="#" method="POST" enctype="multipart/form-data">
                    
                    <fieldset class="form-section">
                        <legend>Personal Information</legend>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullName" name="fullName" class="input-field" value="John Doe" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" class="input-field" value="johndoe_99" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="input-field" value="john.doe@example.com" required>
                        </div>
                    </fieldset>

                    <fieldset class="form-section">
                        <legend>Profile Photo</legend>
                        <div class="form-group">
                            <label for="profilePhoto">Upload New Photo</label>
                            <input type="file" id="profilePhoto" name="profilePhoto" class="input-field" accept="image/png, image/jpeg">
                        </div>
                    </fieldset>

                    <fieldset class="form-section">
                        <legend>Security</legend>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="newPassword">New Password</label>
                                <input type="password" id="newPassword" name="newPassword" class="input-field" placeholder="Leave blank to keep current">
                            </div>
                            
                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" class="input-field" placeholder="Confirm new password">
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-actions">
                        <button type="button" class="button button-outline">Cancel</button>
                        <button type="submit" class="button">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</body>
</html>
<?php
drawFooter();

?>
