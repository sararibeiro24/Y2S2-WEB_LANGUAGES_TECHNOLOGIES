document.addEventListener('DOMContentLoaded', function () {

    const filterSearch     = document.getElementById('filterSearch');
    const filterTrainer    = document.getElementById('filterTrainer');
    const filterDifficulty = document.getElementById('filterDifficulty');
    const filterDate       = document.getElementById('filterDate');

    function getFilterParams() {
        const params = {};
        const query = filterSearch ? filterSearch.value.trim() : '';
        if (query) params.query = query;
        const trainer = filterTrainer ? filterTrainer.value : '';
        if (trainer) params.trainer_id = trainer;
        const diff = filterDifficulty ? filterDifficulty.value : '';
        if (diff) params.difficulty = diff;
        const date = filterDate ? filterDate.value : '';
        if (date) params.date = date;
        return params;
    }

    function applyFilters() {
        const params = getFilterParams();
        if (params.date) {
            const d = new Date(params.date + 'T00:00:00');
            const monday = getMonday(d);
            const weekStr = formatDate(monday);
            loadWeek(weekStr, true, params);
        } else {
            const grid = document.getElementById('calendarGrid');
            const week = grid ? grid.dataset.week : CURRENT_WEEK;
            loadWeek(week || CURRENT_WEEK, true, params);
        }
    }

    window.clearFilters = function () {
        if (filterSearch) filterSearch.value = '';
        if (filterTrainer) filterTrainer.value = '';
        if (filterDifficulty) filterDifficulty.value = '';
        if (filterDate) filterDate.value = '';
        const grid = document.getElementById('calendarGrid');
        const week = grid ? grid.dataset.week : CURRENT_WEEK;
        loadWeek(week || CURRENT_WEEK, true, {});
    };

    if (filterSearch) {
        let debounceTimer;
        filterSearch.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(applyFilters, 300);
        });
    }
    if (filterTrainer) filterTrainer.addEventListener('change', applyFilters);
    if (filterDifficulty) filterDifficulty.addEventListener('change', applyFilters);
    if (filterDate) filterDate.addEventListener('change', applyFilters);

    window.navigateWeek = function (direction) {
        const grid = document.getElementById('calendarGrid');
        const currentWeek = grid ? grid.dataset.week : CURRENT_WEEK;
        if (!currentWeek) return;
        let d = new Date(currentWeek);
        if (direction !== 0) {
            d.setDate(d.getDate() + direction * 7);
        } else {
            d = new Date();
        }
        const monday = getMonday(d);
        const weekStr = formatDate(monday);
        const params = getFilterParams();
        loadWeek(weekStr, true, params);
    };

    window.openClassModal = function (scheduleId, clearComment) {
        if (clearComment === undefined) clearComment = false;
        const body = document.getElementById('classModalBody');
        body.innerHTML = '<p style="text-align: center; color: #888;">Loading...</p>';
        showModal('classModal');

        fetch('../actions/api_class_detail.php?id=' + scheduleId)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.error) {
                    body.innerHTML = '<p>Class not found.</p>';
                    return;
                }
                if (clearComment && data.user_review) {
                    data.user_review.comment = '';
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

    document.querySelectorAll('.modal-overlay').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
});

function getMonday(d) {
    const date = new Date(d);
    const day = date.getDay();
    const diff = date.getDate() - day + (day === 0 ? -6 : 1);
    date.setDate(diff);
    date.setHours(0, 0, 0, 0);
    return date;
}

function formatDate(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + day;
}

function showModal(id) {
    document.getElementById(id).classList.add('active');
}

function getFilterQueryString(params) {
    let qs = '';
    if (params.query) qs += '&query=' + encodeURIComponent(params.query);
    if (params.trainer_id) qs += '&trainer_id=' + encodeURIComponent(params.trainer_id);
    if (params.difficulty) qs += '&difficulty=' + encodeURIComponent(params.difficulty);
    return qs;
}

function loadWeek(weekStr, updateURL, filterParams) {
    if (filterParams === undefined) filterParams = {};
    const grid = document.getElementById('calendarGrid');
    const label = document.getElementById('weekLabel');

    let url = '../actions/api_calendar_week.php?week_start=' + weekStr;
    url += getFilterQueryString(filterParams);

    fetch(url)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.error) {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: red;">Failed to load.</p>';
                return;
            }
            const start = new Date(data.week_start);
            const end = new Date(data.week_start);
            end.setDate(end.getDate() + 6);
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            label.textContent = monthNames[start.getMonth()] + ' ' + start.getDate() + ' \u2013 ' +
                                monthNames[end.getMonth()] + ' ' + end.getDate() + ', ' + end.getFullYear();

            renderCalendar(grid, data.week_start, data.classes || []);
            grid.dataset.week = data.week_start;
            if (updateURL) {
                        let urlPath = '?week=' + data.week_start;
                        const qs = getFilterQueryString(filterParams);
                        if (qs) urlPath += qs;
                        window.history.replaceState({}, '', urlPath);
                    }
        })
        .catch(function () {
            grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: red;">Failed to load classes.</p>';
        });
}

function renderCalendar(grid, weekStart, classes) {
    const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const today = new Date();
    const todayStr = formatDate(today);
    let html = '';

    dayNames.forEach(function (name) {
        html += '<div class="calendar-day-header">' + name + '</div>';
    });

    const byDay = {};
    classes.forEach(function (c) {
        const d = c.scheduled_at.substring(0, 10);
        if (!byDay[d]) byDay[d] = [];
        byDay[d].push(c);
    });

    dayNames.forEach(function (_, i) {
        const d = new Date(weekStart);
        d.setDate(d.getDate() + i);
        const dateStr = formatDate(d);
        const isToday = dateStr === todayStr;
        const dayNum = d.getDate();
        let cls = 'calendar-day';
        if (isToday) cls += ' today';

        html += '<div class="' + cls + '" data-date="' + dateStr + '">';
        html += '<div class="calendar-day-number">' + dayNum + '</div>';

        if (byDay[dateStr]) {
            byDay[dateStr]
                .sort(function (a, b) { return a.scheduled_at.localeCompare(b.scheduled_at); })
                .forEach(function (c) {
                    const isFull = c.enrolled >= c.capacity;
                    const isEnrolled = ENROLLED_IDS && ENROLLED_IDS.some(function (id) {
                        return String(id) === String(c.schedule_id);
                    });
                    const classTime = new Date(c.scheduled_at);
                    const now = new Date();
                    const hasPassed = now > classTime;
                    const time = c.scheduled_at.substring(11, 16);

                    let cardClass = 'calendar-class-card';
                    if (isFull) cardClass += ' full';
                    if (isEnrolled) cardClass += ' enrolled';
                    if (hasPassed) cardClass += ' past';

                    html +=
                        '<div class="' + cardClass + '" ' +
                            'data-id="' + c.schedule_id + '" ' +
                            'onclick="openClassModal(' + c.schedule_id + ')">' +
                            '<div class="ccal-time">'    + time                        + '</div>' +
                            '<div class="ccal-name">'    + escapeHtml(c.name)          + '</div>' +
                            '<div class="ccal-trainer">' + escapeHtml(c.trainer)       + '</div>' +
                            '<div class="ccal-spots">'   + c.enrolled + '/' + c.capacity + '</div>';

                    if (isEnrolled) {
                        html += '<div class="ccal-enrolled-badge">Enrolled</div>';
                    } else if (hasPassed) {
                        html += '<div class="ccal-enrolled-badge" style="background:#555;color:#999;">Past</div>';
                    }

                    html += '</div>';
                });
        }

        html += '</div>';
    });

    grid.innerHTML = html;
}

function renderClassModal(data) {
    const time = data.scheduled_at.substring(11, 16);
    const date = new Date(data.scheduled_at);
    const dateStr = date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
    const isFull = data.enrolled >= data.capacity;
    const diffClass = 'diff-' + (data.difficulty || 'beginner').toLowerCase();

    const now = new Date();
    const classTime = new Date(data.scheduled_at);
    const hasClassPassed = now > classTime;

    const enrolled = ENROLLED_IDS && ENROLLED_IDS.some(function (id) {
        return String(id) === String(data.schedule_id);
    });

    const isOwnClass = USER_ROLE === 'trainer' && USER_ID && Number(data.trainer_id) === Number(USER_ID);

    const photo = data.trainer_photo
    ? '<img src="' + escapeHtml(resolvePhoto(data.trainer_photo)) + '" alt="' + escapeHtml(data.trainer) + '">'
    : '<div class="placeholder">' + getInitials(data.trainer) + '</div>';

    

    const specsHtml = (data.specs_list && data.specs_list.length > 0)
        ? data.specs_list.map(function (s) { return escapeHtml(s.trim()); }).join(' &bull; ')
        : '';

    let titleBadge = '';
    if (enrolled) {
        titleBadge = '<span class="modal-enrolled-badge">Enrolled</span>';
    } else if (hasClassPassed) {
        titleBadge = '<span class="modal-past-badge">Past</span>';
    }

    let subtitleExtra = '';
    if (isOwnClass) {
        subtitleExtra = ' <span class="modal-self-class-badge">Your Class</span>';
    }

    let actionHtml;

    if (isOwnClass) {
        actionHtml = '<button class="button button-small" disabled>Cannot enroll in your own class</button>';
    } else if (hasClassPassed) {
        actionHtml = '<button class="button button-small" disabled>Class Already Happened</button>';
    } else if (isFull && !enrolled) {
        actionHtml = '<button class="button button-small" disabled>Class Full</button>';
    } else if (!IS_LOGGED_IN) {
        actionHtml = '<button class="button button-small" onclick="closeModal(\'classModal\'); showModal(\'authModal\')">Enroll Now</button>';
    } else if (enrolled) {
        actionHtml = '<button class="button button-small button-outline" onclick="unenrollAjax(' + data.schedule_id + ')">Un-enroll</button>';
    } else {
        actionHtml = '<button class="button button-small" onclick="enrollAjax(' + data.schedule_id + ')">Enroll Now</button>';
    }

    document.getElementById('classModalBody').innerHTML =
        '<div class="modal-title">' + escapeHtml(data.name) + titleBadge + '</div>' +
        '<div class="modal-subtitle">' + dateStr + ' &middot; ' + time + ' &middot; ' + escapeHtml(data.trainer) + subtitleExtra + '</div>' +
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
                (data.years_experience
                    ? '<div class="experience">' + data.years_experience + ' years experience</div>'
                    : '') +
            '</div>' +
        '</div>' +
        '<div class="modal-footer">' + actionHtml + '</div>' +
        renderReviewsSection(data);
}

function renderReviewsSection(data) {
    let html = '<div class="modal-reviews">';
    html += '<h4 class="reviews-title">Reviews' +
        (data.review_count ? ' <span class="review-count">(' + data.review_count + ')</span>' : '') +
        (data.avg_rating ? ' <span class="avg-rating">' + renderStars(parseFloat(data.avg_rating)) + ' ' + data.avg_rating + '</span>' : '') +
        '</h4>';

    const now = new Date();
    const classTime = new Date(data.scheduled_at);
    const hasClassPassed = now > classTime;
    const isUserEnrolled = ENROLLED_IDS && ENROLLED_IDS.some(function (id) {
        return String(id).trim() === String(data.schedule_id).trim();
    });

    if (isUserEnrolled && !hasClassPassed) {
        html += '<p class="review-msg review-notice-past">' +
                'You cannot review this class yet because it has not taken place.' +
                '</p>';
    }

    if (data.can_review && !data.user_review && hasClassPassed) {
        const userRating = 5;
        const userComment = '';
        const btnText = 'Submit Review';
        html += '<form class="review-form" onsubmit="submitReview(event, ' + data.schedule_id + ')">' +
            '<div class="star-rating">' +
                '<input type="hidden" name="rating" id="reviewRating_' + data.schedule_id + '" value="' + userRating + '">';
        for (let i = 5; i >= 1; i--) {
            const checked = i === userRating ? ' checked' : '';
            html += '<input type="radio" name="star" id="star' + i + '_' + data.schedule_id + '" value="' + i + '"' + checked + ' onchange="document.getElementById(\'reviewRating_' + data.schedule_id + '\').value=' + i + '">' +
                '<label for="star' + i + '_' + data.schedule_id + '" title="' + i + ' stars">&#9733;</label>';
        }
        html += '</div>' +
            '<textarea name="comment" placeholder="Share your experience..." maxlength="500" rows="3">' + userComment + '</textarea>' +
            '<button class="button button-small">' + btnText + '</button>' +
            '<span class="review-msg" id="reviewMsg_' + data.schedule_id + '"></span>' +
            '</form>';
    }
    else if (data.user_review) {
        html += '<p class="review-msg review-msg-already">You have already reviewed this class. Thank you!</p>';
    }

    if (data.reviews && data.reviews.length > 0) {
        html += '<div class="reviews-list">';
        data.reviews.forEach(function (r) {
            const photo = r.user_photo
            ? '<img src="' + escapeHtml(resolvePhoto(r.user_photo)) + '" alt="' + escapeHtml(r.user_name) + '">'
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

    if (!data.reviews || (data.reviews.length === 0 && !data.can_review)) {
        html += '<p class="no-reviews">No reviews yet.</p>';
    }

    html += '</div>';
    return html;
}

function renderStars(rating) {
    const full = Math.floor(rating);
    const half = (rating - full) >= 0.5;
    let html = '';
    for (let i = 0; i < full; i++) html += '<span class="star full">&#9733;</span>';
    if (half) html += '<span class="star half">&#9733;</span>';
    for (let i = full + (half ? 1 : 0); i < 5; i++) html += '<span class="star empty">&#9734;</span>';
    return html;
}

window.submitReview = function (event, scheduleId) {
    event.preventDefault();
    const form = event.target;
    const rating = document.getElementById('reviewRating_' + scheduleId).value;
    const comment = form.querySelector('textarea').value;
    const msgEl = document.getElementById('reviewMsg_' + scheduleId);
    const fd = new FormData();
    fd.append('schedule_id', scheduleId);
    fd.append('rating', rating);
    fd.append('comment', comment);

    fetch('../actions/action_add_review.php', { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                msgEl.textContent = 'Review saved!';
                msgEl.className = 'review-msg success';
                openClassModal(scheduleId, true);
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

function enrollXhr(scheduleId, onSuccess, onError) {
    const fd = new FormData();
    fd.append('schedule_id', scheduleId);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../actions/action_enroll.php');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function () {
        if (xhr.status === 200) { onSuccess(); }
        else {
            let msg = 'Could not enroll.';
            try { const d = JSON.parse(xhr.responseText); if (d.error) msg = d.error; } catch (e) {}
            onError(msg);
        }
    };
    xhr.onerror = function () { onError('Network error.'); };
    xhr.send(fd);
}

function unenrollXhr(scheduleId, onSuccess, onError) {
    const fd = new FormData();
    fd.append('schedule_id', scheduleId);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../actions/action_unenroll.php');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function () {
        if (xhr.status === 200) { onSuccess(); }
        else {
            let msg = 'Could not un-enroll.';
            try { const d = JSON.parse(xhr.responseText); if (d.error) msg = d.error; } catch (e) {}
            onError(msg);
        }
    };
    xhr.onerror = function () { onError('Network error.'); };
    xhr.send(fd);
}

window.enrollAjax = function (scheduleId) {
    if (!IS_LOGGED_IN) { closeModal('classModal'); showModal('authModal'); return; }
    enrollXhr(
        scheduleId,
        function () {
            const idStr = String(scheduleId);
            if (!ENROLLED_IDS.map(String).includes(idStr)) {
                ENROLLED_IDS.push(idStr);
            }
            openClassModal(scheduleId);
            const grid = document.getElementById('calendarGrid');
            if (grid && grid.dataset.week) {
                const params = {};
                const fs = document.getElementById('filterSearch');
                const ft = document.getElementById('filterTrainer');
                const fd = document.getElementById('filterDifficulty');
                if (fs && fs.value.trim()) params.query = fs.value.trim();
                if (ft && ft.value) params.trainer_id = ft.value;
                if (fd && fd.value) params.difficulty = fd.value;
                loadWeek(grid.dataset.week, false, params);
            }
        },
        function (msg) { showAlertModal(msg); }
    );
};

window.unenrollAjax = function (scheduleId) {
    showConfirmModal('Are you sure you want to un-enroll from this class?', function () {
        unenrollXhr(
            scheduleId,
            function () {
                ENROLLED_IDS = ENROLLED_IDS.filter(function (id) {
                    return String(id).trim() !== String(scheduleId).trim();
                });

                openClassModal(scheduleId);
                const grid = document.getElementById('calendarGrid');
                if (grid && grid.dataset.week) {
                    const params = {};
                    const fs = document.getElementById('filterSearch');
                    const ft = document.getElementById('filterTrainer');
                    const fd = document.getElementById('filterDifficulty');
                    if (fs && fs.value.trim()) params.query = fs.value.trim();
                    if (ft && ft.value) params.trainer_id = ft.value;
                    if (fd && fd.value) params.difficulty = fd.value;
                    loadWeek(grid.dataset.week, false, params);
                }
            },
            function (msg) { showAlertModal(msg); }
        );
    });
};

function showConfirmModal(message, onConfirm) {
    const overlay = document.getElementById('confirmModal');
    document.getElementById('confirmMsg').textContent = message;

    const yesBtn = document.getElementById('confirmYes');
    const noBtn  = document.getElementById('confirmNo');
    const newYes = yesBtn.cloneNode(true);
    const newNo  = noBtn.cloneNode(true);
    yesBtn.parentNode.replaceChild(newYes, yesBtn);
    noBtn.parentNode.replaceChild(newNo,  noBtn);
    newYes.addEventListener('click', function () { overlay.classList.remove('active'); onConfirm(); });
    newNo.addEventListener('click',  function () { overlay.classList.remove('active'); });

    overlay.classList.add('active');
}

function showAlertModal(message) {
    const overlay = document.getElementById('alertModal');
    document.getElementById('alertMsg').textContent = message;

    const okBtn = document.getElementById('alertOk');
    const newOk = okBtn.cloneNode(true);
    okBtn.parentNode.replaceChild(newOk, okBtn);
    newOk.addEventListener('click', function () { overlay.classList.remove('active'); });

    overlay.classList.add('active');
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function getInitials(name) {
    if (!name) return '?';
    const parts = name.split(' ');
    return (parts[0] ? parts[0][0] : '') + (parts[1] ? parts[1][0] : '');
}
function resolvePhoto(path) {
    if (!path) return null;
    path = path.replace(/^\/+/, ''); 
    if (path.startsWith('uploads/')) return '../' + path;
    return '../img/' + path;
}