document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('trainerSearch');
    var grid = document.getElementById('trainerGrid');

    if (!searchInput || !grid) return;

    window.openTrainerModal = function (trainerId) {
        var body = document.getElementById('trainerModalBody');
        body.innerHTML = '<p style="text-align: center; color: #888;">Loading...</p>';
        showModal('trainerModal');

        fetch('../actions/api_trainer_detail.php?id=' + trainerId)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.error) {
                    body.innerHTML = '<p>Trainer not found.</p>';
                    return;
                }
                renderTrainerModal(data);
            })
            .catch(function () {
                body.innerHTML = '<p>Failed to load trainer details.</p>';
            });
    };

    function fetchTrainers() {
        var params = new URLSearchParams();
        var q = searchInput.value.trim();
        if (q) params.set('search', q);

        grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #888;">Loading...</p>';

        fetch('../actions/api_trainers.php?' + params.toString())
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.length === 0) {
                    grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #888;">No trainers found.</p>';
                    return;
                }
                grid.innerHTML = '';
                data.forEach(function (t) {
                    var wrapper = document.createElement('div');
                    wrapper.setAttribute('onclick', 'openTrainerModal(' + t.id + ')');
                    wrapper.appendChild(buildTrainerCard(t));
                    grid.appendChild(wrapper);
                });
            })
            .catch(function () {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: red;">Failed to load trainers.</p>';
            });
    }

    function buildTrainerCard(t) {
        var div = document.createElement('div');
        div.className = 'team-member trainer-card-clickable';

        var initials = t.name.split(' ').map(function (s) { return s[0]; }).join('').toUpperCase().substring(0, 2);
        var specsHtml = '';
        if (t.specializations_list && t.specializations_list.length > 1) {
            specsHtml = '<div class="trainer-specs">';
            t.specializations_list.forEach(function (s) {
                specsHtml += '<span class="trainer-spec-tag">' + escapeHtml(s.trim()) + '</span>';
            });
            specsHtml += '</div>';
        }

        var firstSpec = t.specializations_list && t.specializations_list[0] ? escapeHtml(t.specializations_list[0]) : 'Fitness';

        div.innerHTML =
            (t.profile_photo
                ? '<img src="../img/' + escapeHtml(t.profile_photo) + '" alt="' + escapeHtml(t.name) + '" class="team-member-image">'
                : '<div class="team-member-image trainer-placeholder">' + initials + '</div>'
            ) +
            '<div class="team-member-info">' +
                '<h3 class="team-member-name">' + escapeHtml(t.name) + '</h3>' +
                '<p class="team-member-role">' + firstSpec + '</p>' +
                (t.bio ? '<p class="team-member-description">' + escapeHtml(t.bio) + '</p>' : '') +
                specsHtml +
            '</div>';

        return div;
    }

    function renderTrainerModal(data) {
        var photo = data.profile_photo
            ? '<img src="../img/' + escapeHtml(data.profile_photo) + '" alt="' + escapeHtml(data.name) + '" class="team-member-image" style="height:250px;">'
            : '<div class="team-member-image trainer-placeholder" style="height:250px;">' + getInitials(data.name) + '</div>';

        var specsHtml = '';
        if (data.specializations_list && data.specializations_list.length > 0) {
            specsHtml = data.specializations_list.map(function (s) {
                return '<span class="trainer-spec-tag">' + escapeHtml(s.trim()) + '</span>';
            }).join(' ');
        }

        var classesHtml = '';
        if (data.classes && data.classes.length > 0) {
            classesHtml = '<div class="modal-trainer-classes"><h4>Classes Taught</h4>';
            data.classes.forEach(function (c) {
                classesHtml += '<div class="modal-trainer-class-item"><span class="tc-name">' + escapeHtml(c.name) + '</span><span class="tc-count">' + c.session_count + ' sessions</span></div>';
            });
            classesHtml += '</div>';
        }

        var upcomingHtml = '';
        if (data.upcoming_sessions && data.upcoming_sessions.length > 0) {
            upcomingHtml = '<div class="modal-trainer-classes" style="margin-top:1em"><h4>Upcoming Sessions</h4>';
            data.upcoming_sessions.forEach(function (s) {
                var d = new Date(s.scheduled_at);
                var dateStr = d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                var time = s.scheduled_at.substring(11, 16);
                upcomingHtml += '<div class="modal-trainer-class-item"><span class="tc-name">' + escapeHtml(s.name) + '</span><span style="color:#999;font-size:0.85em;">' + dateStr + ' ' + time + '</span></div>';
            });
            upcomingHtml += '</div>';
        }

        var yearsText = data.years_experience ? data.years_experience + ' years experience' : '';

        document.getElementById('trainerModalBody').innerHTML =
            photo +
            '<div style="padding: 1.5em;">' +
                '<div class="modal-title">' + escapeHtml(data.name) + '</div>' +
                (yearsText ? '<div style="color: var(--ladybug-red); font-size: 0.9em; margin-bottom: 0.5em;">' + yearsText + '</div>' : '') +
                '<div style="margin-bottom: 1em;">' + specsHtml + '</div>' +
                (data.bio ? '<div class="modal-body"><p>' + escapeHtml(data.bio) + '</p></div>' : '') +
                (data.certifications ? '<div class="modal-body"><p><strong>Certifications:</strong> ' + escapeHtml(data.certifications) + '</p></div>' : '') +
                classesHtml +
                upcomingHtml +
                '<div class="modal-footer">' +
                    '<a href="schedule.php" class="button button-small button-outline">View Schedule</a>' +
                    '<button class="button button-small button-outline" onclick="closeModal(\'trainerModal\')">Close</button>' +
                '</div>' +
            '</div>';
    }

    window.closeModal = function (id) {
        document.getElementById(id).classList.remove('active');
    };

    document.querySelectorAll('.modal-overlay').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('active');
        });
    });

    var debounceTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchTrainers, 300);
    });
});

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

function showModal(id) {
    document.getElementById(id).classList.add('active');
}
