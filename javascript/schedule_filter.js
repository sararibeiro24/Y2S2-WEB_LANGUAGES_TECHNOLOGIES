document.addEventListener('DOMContentLoaded', function () {

    // Expose functions globally for onclick handlers
    window.navigateWeek = function (direction) {
        var grid = document.getElementById('calendarGrid');
        var currentWeek = grid.dataset.week;
        if (!currentWeek) return;
        var d = new Date(currentWeek);
        if (direction !== 0) {
            d.setDate(d.getDate() + direction * 7);
        } else {
            d = new Date();
        }
        var monday = getMonday(d);
        var weekStr = formatDate(monday);
        loadWeek(weekStr);
    };

    window.openClassModal = function (scheduleId) {
        var body = document.getElementById('classModalBody');
        body.innerHTML = '<p style="text-align: center; color: #888;">Loading...</p>';
        showModal('classModal');

        fetch('../actions/api_class_detail.php?id=' + scheduleId)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.error) {
                    body.innerHTML = '<p>Class not found.</p>';
                    return;
                }
                renderClassModal(data);
            })
            .catch(function () {
                body.innerHTML = '<p>Failed to load class details.</p>';
            });
    };

    window.closeModal = function (id) {
        document.getElementById(id).classList.remove('active');
    };

    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
});

function getMonday(d) {
    var date = new Date(d);
    var day = date.getDay();
    var diff = date.getDate() - day + (day === 0 ? -6 : 1);
    date.setDate(diff);
    date.setHours(0, 0, 0, 0);
    return date;
}

function formatDate(d) {
    var y = d.getFullYear();
    var m = String(d.getMonth() + 1).padStart(2, '0');
    var day = String(d.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + day;
}

function showModal(id) {
    document.getElementById(id).classList.add('active');
}

function loadWeek(weekStr) {
    var grid = document.getElementById('calendarGrid');
    var label = document.getElementById('weekLabel');

    grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #888; padding: 2em;">Loading...</p>';

    fetch('../actions/api_calendar_week.php?week_start=' + weekStr)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.error) {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: red;">Failed to load.</p>';
                return;
            }
            var start = new Date(data.week_start);
            var end = new Date(data.week_start);
            end.setDate(end.getDate() + 6);
            var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            label.textContent = monthNames[start.getMonth()] + ' ' + start.getDate() + ' \u2013 ' +
                                monthNames[end.getMonth()] + ' ' + end.getDate() + ', ' + end.getFullYear();

            renderCalendar(grid, data.week_start, data.classes || []);
            grid.dataset.week = data.week_start;
            window.history.replaceState({}, '', '?week=' + data.week_start);
        })
        .catch(function () {
            grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: red;">Failed to load classes.</p>';
        });
}

function renderCalendar(grid, weekStart, classes) {
    var dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    var today = new Date();
    var todayStr = formatDate(today);
    var html = '';

    // Headers
    dayNames.forEach(function (name) {
        html += '<div class="calendar-day-header">' + name + '</div>';
    });

    // Group by day
    var byDay = {};
    classes.forEach(function (c) {
        var d = c.scheduled_at.substring(0, 10);
        if (!byDay[d]) byDay[d] = [];
        byDay[d].push(c);
    });

    // Day cells
    dayNames.forEach(function (_, i) {
        var d = new Date(weekStart);
        d.setDate(d.getDate() + i);
        var dateStr = formatDate(d);
        var isToday = dateStr === todayStr;
        var dayNum = d.getDate();
        var cls = 'calendar-day';
        if (isToday) cls += ' today';

        html += '<div class="' + cls + '" data-date="' + dateStr + '">';
        html += '<div class="calendar-day-number">' + dayNum + '</div>';

        if (byDay[dateStr]) {
            byDay[dateStr].sort(function (a, b) {
                return a.scheduled_at.localeCompare(b.scheduled_at);
            });
            byDay[dateStr].forEach(function (c) {
                var isFull = c.enrolled >= c.capacity;
                var time = c.scheduled_at.substring(11, 16);
                html += '<div class="calendar-class-card' + (isFull ? ' full' : '') + '" data-id="' + c.schedule_id + '" onclick="openClassModal(' + c.schedule_id + ')">' +
                    '<div class="ccal-time">' + time + '</div>' +
                    '<div class="ccal-name">' + escapeHtml(c.name) + '</div>' +
                    '<div class="ccal-trainer">' + escapeHtml(c.trainer) + '</div>' +
                    '<div class="ccal-spots">' + c.enrolled + '/' + c.capacity + '</div>' +
                '</div>';
            });
        }

        html += '</div>';
    });

    grid.innerHTML = html;
}

function renderClassModal(data) {
    var time = data.scheduled_at.substring(11, 16);
    var date = new Date(data.scheduled_at);
    var dateStr = date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
    var isFull = data.enrolled >= data.capacity;
    var diffClass = 'diff-' + (data.difficulty || 'beginner').toLowerCase();

    var enrolled = ENROLLED_IDS && ENROLLED_IDS.includes(String(data.schedule_id));

    var photo = data.trainer_photo
        ? '<img src="../img/' + escapeHtml(data.trainer_photo) + '" alt="' + escapeHtml(data.trainer) + '">'
        : '<div class="placeholder">' + getInitials(data.trainer) + '</div>';

    var specsHtml = '';
    if (data.specs_list && data.specs_list.length > 0) {
        specsHtml = data.specs_list.map(function (s) { return escapeHtml(s.trim()); }).join(' &bull; ');
    }

    var actionHtml;
    if (isFull) {
        actionHtml = '<button class="button button-small" disabled>Class Full</button>';
    } else if (!IS_LOGGED_IN) {
        actionHtml = '<button class="button button-small" onclick="closeModal(\'classModal\'); showModal(\'authModal\')">Enroll Now</button>';
    } else if (enrolled) {
        actionHtml = '<form action="../actions/action_unenroll.php" method="post" style="display:inline">' +
            '<input type="hidden" name="schedule_id" value="' + data.schedule_id + '">' +
            '<button class="button button-small button-outline" style="border-color: #888; color: #888;">Un-enroll</button></form>';
    } else {
        actionHtml = '<form action="../actions/action_enroll.php" method="post" style="display:inline">' +
            '<input type="hidden" name="schedule_id" value="' + data.schedule_id + '">' +
            '<button class="button button-small">Enroll Now</button></form>';
    }

    document.getElementById('classModalBody').innerHTML =
        '<div class="modal-title">' + escapeHtml(data.name) + '</div>' +
        '<div class="modal-subtitle">' + dateStr + ' &middot; ' + time + ' &middot; ' + escapeHtml(data.trainer) + '</div>' +
        '<div class="modal-difficulty ' + diffClass + '">' + escapeHtml(data.difficulty || 'Beginner') + '</div>' +
        '<div class="modal-body">' +
            '<p>' + escapeHtml(data.description) + '</p>' +
            '<p><strong>Spots:</strong> ' + data.enrolled + ' / ' + data.capacity + '</p>' +
            '<p><strong>Duration:</strong> 60 min</p>' +
        '</div>' +
        '<div class="modal-trainer-info">' +
            photo +
            '<div>' +
                '<div class="mt-name">' + escapeHtml(data.trainer) + '</div>' +
                '<div class="mt-spec">' + specsHtml + '</div>' +
                (data.years_experience ? '<div style="color: #999; font-size: 0.85em; margin-top: 0.3em;">' + data.years_experience + ' years experience</div>' : '') +
            '</div>' +
        '</div>' +
        '<div class="modal-footer">' + actionHtml + '</div>' +
        renderReviewsSection(data);
}

function renderReviewsSection(data) {
    var html = '<div class="modal-reviews">';
    html += '<h4 class="reviews-title">Reviews' +
        (data.review_count ? ' <span class="review-count">(' + data.review_count + ')</span>' : '') +
        (data.avg_rating ? ' <span class="avg-rating">' + renderStars(parseFloat(data.avg_rating)) + ' ' + data.avg_rating + '</span>' : '') +
        '</h4>';

    // User review form
    if (data.can_review) {
        var userRating = data.user_review ? data.user_review.rating : 5;
        var userComment = data.user_review ? escapeHtml(data.user_review.comment) : '';
        var btnText = data.user_review ? 'Update Review' : 'Submit Review';
        html += '<form class="review-form" onsubmit="submitReview(event, ' + data.schedule_id + ')">' +
            '<div class="star-rating">' +
                '<input type="hidden" name="rating" id="reviewRating_' + data.schedule_id + '" value="' + userRating + '">';
        for (var i = 5; i >= 1; i--) {
            var checked = i === userRating ? ' checked' : '';
            html += '<input type="radio" name="star" id="star' + i + '_' + data.schedule_id + '" value="' + i + '"' + checked + ' onchange="document.getElementById(\'reviewRating_' + data.schedule_id + '\').value=' + i + '">' +
                '<label for="star' + i + '_' + data.schedule_id + '" title="' + i + ' stars">&#9733;</label>';
        }
        html += '</div>' +
            '<textarea name="comment" placeholder="Share your experience..." maxlength="500" rows="3">' + userComment + '</textarea>' +
            '<button class="button button-small">' + btnText + '</button>' +
            '<span class="review-msg" id="reviewMsg_' + data.schedule_id + '"></span>' +
            '</form>';
    }

    // Existing reviews
    if (data.reviews && data.reviews.length > 0) {
        html += '<div class="reviews-list">';
        data.reviews.forEach(function (r) {
            var photo = r.user_photo
                ? '<img src="../img/' + escapeHtml(r.user_photo) + '" alt="' + escapeHtml(r.user_name) + '">'
                : '<div class="review-avatar-placeholder">' + getInitials(r.user_name) + '</div>';
            html += '<div class="review-item">' +
                '<div class="review-header">' +
                    photo +
                    '<div>' +
                        '<strong>' + escapeHtml(r.user_name) + '</strong>' +
                        '<div class="review-stars">' + renderStars(r.rating) + '</div>' +
                    '</div>' +
                '</div>' +
                (r.comment ? '<p class="review-comment">' + escapeHtml(r.comment) + '</p>' : '') +
                '</div>';
        });
        html += '</div>';
    }

    if (!data.reviews || data.reviews.length === 0 && !data.can_review) {
        html += '<p class="no-reviews">No reviews yet.</p>';
    }

    html += '</div>';
    return html;
}

function renderStars(rating) {
    var full = Math.floor(rating);
    var half = rating - full >= 0.5;
    var html = '';
    for (var i = 0; i < full; i++) html += '<span class="star full">&#9733;</span>';
    if (half) html += '<span class="star half">&#9733;</span>';
    for (var i = full + (half ? 1 : 0); i < 5; i++) html += '<span class="star empty">&#9734;</span>';
    return html;
}

window.submitReview = function (event, scheduleId) {
    event.preventDefault();
    var form = event.target;
    var rating = document.getElementById('reviewRating_' + scheduleId).value;
    var comment = form.querySelector('textarea').value;
    var msgEl = document.getElementById('reviewMsg_' + scheduleId);

    var fd = new FormData();
    fd.append('schedule_id', scheduleId);
    fd.append('rating', rating);
    fd.append('comment', comment);

    fetch('../actions/action_add_review.php', { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                msgEl.textContent = 'Review saved!';
                msgEl.className = 'review-msg success';
                // Reload modal to show updated reviews
                openClassModal(scheduleId);
            } else {
                msgEl.textContent = data.error || 'Failed to save review.';
                msgEl.className = 'review-msg error';
            }
        })
        .catch(function () {
            msgEl.textContent = 'Network error.';
            msgEl.className = 'review-msg error';
        });
};

function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function getInitials(name) {
    if (!name) return '?';
    var parts = name.split(' ');
    return (parts[0] ? parts[0][0] : '') + (parts[1] ? parts[1][0] : '');
}
