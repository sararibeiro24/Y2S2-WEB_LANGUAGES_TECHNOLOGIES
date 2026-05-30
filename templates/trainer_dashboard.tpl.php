<?php
declare(strict_types=1);

function drawTrainerDashboard(string $bio, string $specializations, string $certifications, array $classes, array $nutritionRequests, PDO $db): void {
    ?>
    <main class="container" style="padding-top: 1em;">
        <div class="page-header">
            <h2>Trainer Dashboard</h2>
            <p>Manage your profile, approve nutrition requests, and review class rosters.</p>
        </div>

        <div class="dashboard-layout">
            <section class="dashboard-card">
                <h3>Your Profile</h3>
                <form action="../actions/action_update_trainer_profile.php" method="post" class="trainer-profile-form">
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="4" maxlength="500"><?= htmlspecialchars($bio) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="specializations">Specializations <span class="field-hint">(comma-separated)</span></label>
                        <input type="text" id="specializations" name="specializations" value="<?= htmlspecialchars($specializations) ?>" placeholder="e.g. Yoga, Pilates, Meditation">
                    </div>
                    <div class="form-group">
                        <label for="certifications">Certifications <span class="field-hint">(comma-separated)</span></label>
                        <input type="text" id="certifications" name="certifications" value="<?= htmlspecialchars($certifications) ?>" placeholder="e.g. NASM, ACE, Yoga Alliance">
                    </div>
                    <button class="button" type="submit">Save Profile</button>
                </form>
            </section>

            <section class="dashboard-card nutrition-requests-card">
                <h3>Nutrition Plan Requests</h3>
                <?php if (empty($nutritionRequests)): ?>
                    <p class="empty-state">No nutrition plan requests yet.</p>
                <?php else: ?>
                    <div class="nutrition-requests-list">
                        <?php foreach ($nutritionRequests as $request): ?>
                            <article class="nutrition-request-card">
                                <div class="request-header">
                                    <div>
                                        <strong><?= htmlspecialchars($request['member_name']) ?></strong>
                                        <span class="request-email"><?= htmlspecialchars($request['member_email']) ?></span>
                                    </div>
                                    <span class="request-goal"><?= htmlspecialchars($request['goal']) ?></span>
                                </div>
                                <div class="request-meta">
                                    <span><strong>Calories:</strong> <?= htmlspecialchars((string)($request['target_calories'] ?? 'Custom')) ?> kcal</span>
                                    <span>Requested <?= htmlspecialchars((new DateTime($request['created_at']))->format('M d, Y')) ?></span>
                                </div>
                                <p class="meal-summary"><?= nl2br(htmlspecialchars($request['meal_details'])) ?></p>
                                <form class="nutrition-request-form" action="../actions/action_approve_nutrition.php" method="POST">
                                    <input type="hidden" name="nutrition_id" value="<?= htmlspecialchars((string)$request['id']) ?>">
                                    <label for="meal_details_<?= htmlspecialchars((string)$request['id']) ?>">Add meal plan details</label>
                                    <textarea id="meal_details_<?= htmlspecialchars((string)$request['id']) ?>" name="meal_details" rows="4" placeholder="Write the nutrition plan details here..." required></textarea>
                                    <button type="submit" class="button">Approve Request</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section class="dashboard-card">
                <h3>Your Upcoming Classes</h3>
                 <a href="manage_classes.php" class="button button-small">Manage Classes</a>
                <?php if (empty($classes)): ?>
                    <p class="empty-state">You have no upcoming classes scheduled.</p>
                <?php else: ?>
                    <?php 
                    foreach ($classes as $c) {
                        drawClassRosterBlock($c, $db);
                    }
                    ?>
                <?php endif; ?>
            </section>
        </div>
    </main>
    <?php
}

function drawClassRosterBlock(array $c, PDO $db): void {
    $isFull = $c['enrolled'] >= $c['capacity'];
    $date = date('D, M j', strtotime($c['scheduled_at']));
    $time = date('H:i', strtotime($c['scheduled_at']));
    $roster = ClassSchedule::getEnrolledMembers($c['schedule_id'], $db);
    ?>
    <div class="class-roster-block">
        <div class="class-roster-header">
            <div>
                <strong><?= htmlspecialchars($c['name']) ?></strong>
                <span class="roster-meta"><?= $date ?> &middot; <?= $time ?> &middot; <?= htmlspecialchars($c['difficulty']) ?></span>
            </div>
            <span class="roster-count <?= $isFull ? 'full' : '' ?>"><?= (int)$c['enrolled'] ?>/<?= (int)$c['capacity'] ?> enrolled</span>
        </div>
        
        <?php if (!empty($roster)): ?>
            <table class="roster-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Email</th>
                        <th>Enrolled</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roster as $m): ?>
                        <tr>
                            <td>
                                <div class="roster-member">
                                    <?php if (!empty($m['profile_photo'])): ?>
                                        <img src="<?= htmlspecialchars(resolvePhoto($m['profile_photo'])) ?>" alt="" class="roster-photo">
                                    <?php else: ?>
                                        <div class="roster-photo-placeholder"><?= htmlspecialchars(strtoupper(substr($m['name'] ?? 'U', 0, 1))) ?></div>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($m['name']) ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($m['email']) ?></td>
                            <td><?= date('M j, H:i', strtotime($m['enrolled_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty-state" style="margin: 0.5em 0 0; font-size: 0.9em;">No members enrolled yet.</p>
        <?php endif; ?>
    </div>
    <?php
}