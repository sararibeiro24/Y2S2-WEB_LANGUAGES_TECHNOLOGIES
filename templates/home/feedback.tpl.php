<?php
function drawFeedback(array $feedbacks, string $feedbackName = ''): void {
?>
<section id="feedback" class="teaser-section bg-news">
    <div class="container">
        <h2>WHAT OUR MEMBERS SAY</h2>
        <p class="subtitle large-subtitle">Hear it from the hive. Real stories, real results.</p>
        <div class="cards-grid cards-grid-3">
        <?php foreach ($feedbacks as $fb): ?>
        <div class="feedback-card">
            <p class="feedback-quote">"<?= htmlspecialchars($fb['message']) ?>"</p>
            <p class="feedback-author">
                - <?= htmlspecialchars($fb['name']) ?>
                <?= str_repeat('⭐', (int)$fb['rating']) ?>
            </p>
        </div>
        <?php endforeach; ?>
    </div>

        <a href="javascript:void(0)" class="button feedback-trigger" style="margin-top: 2em;">LEAVE YOUR FEEDBACK</a>

        <div id="feedbackModal" class="modal-overlay" style="display:none;">
            <div class="modal-content card">
                <span id="closeFeedbackBtn" class="modal-close">&times;</span>
                <h2>Leave Your Feedback</h2>
                <form action="../actions/action_submit_feedback.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                     <div class="form-group">
                        <?php if (Session::isLoggedIn()): ?>
                            <input type="hidden" name="name" value="<?= htmlspecialchars($feedbackName) ?>">
                            <p><strong>Submitting as:</strong> <?= htmlspecialchars($feedbackName) ?></p>
                        <?php else: ?>
                            <label for="feedbackName">Your Name</label>
                            <input type="text" id="feedbackName" name="name" class="input-field" required>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Rating</label>
                        <div class="star-rating">
                            <input type="hidden" name="rating" id="feedbackRatingValue" value="5">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="star" id="fstar<?= $i ?>" value="<?= $i ?>"
                                       <?= $i === 5 ? 'checked' : '' ?>
                                       onchange="document.getElementById('feedbackRatingValue').value=<?= $i ?>">
                                <label for="fstar<?= $i ?>" title="<?= $i ?> stars">&#9733;</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="feedbackMessage">Your Feedback</label>
                        <textarea id="feedbackMessage" name="message" class="input-field" rows="4"
                                  maxlength="500" required></textarea>
                    </div>
                    <button type="submit" class="button">Submit</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php 
}