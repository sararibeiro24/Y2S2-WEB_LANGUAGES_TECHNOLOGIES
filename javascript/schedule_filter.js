document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const grid = document.getElementById('classGrid');
    const searchInput = document.getElementById('searchQuery');
    const trainerSelect = document.getElementById('trainerSelect');
    const dateInput = document.getElementById('classDate');

    function fetchClasses() {
        const params = new URLSearchParams();
        if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
        if (trainerSelect.value !== 'all') params.set('trainer', trainerSelect.value);
        if (dateInput.value) params.set('date', dateInput.value);

        grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #888;">Loading...</p>';

        fetch('../actions/api_classes.php?' + params.toString())
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.length === 0) {
                    grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #888;">No classes found.</p>';
                    return;
                }
                grid.innerHTML = '';
                data.forEach(function (c) {
                    var card = document.createElement('article');
                    card.className = 'card class-card';

                    var enrolled = parseInt(c.enrolled);
                    var capacity = parseInt(c.capacity);
                    var statusText = 'Available';
                    var badgeClass = 'badge-green';
                    var isFull = false;

                    if (enrolled >= capacity) {
                        statusText = 'Full';
                        badgeClass = 'badge-red';
                        isFull = true;
                    } else if (enrolled >= capacity - 1) {
                        statusText = 'Few Spots';
                        badgeClass = 'badge-yellow';
                    }

                    var time = c.scheduled_at.substring(11, 16);

                    card.innerHTML =
                        '<div class="class-time">' + time + '</div>' +
                        '<span class="badge ' + badgeClass + '">' + statusText + '</span>' +
                        '<h3 class="class-title">' + escapeHtml(c.name) + '</h3>' +
                        '<p class="class-trainer">with ' + escapeHtml(c.trainer) + '</p>' +
                        '<div class="class-meta">' +
                            '<span><img src="../img/relogio.png" alt="Clock">60 min</span>' +
                            '<span><img src="../img/follower.png" alt="Users">' + enrolled + ' / ' + capacity + ' spots</span>' +
                        '</div>' +
                        (isFull
                            ? '<button class="button button-small" disabled>Full</button>'
                            : '<form action="../actions/action_enroll.php" method="post">' +
                                '<input type="hidden" name="schedule_id" value="' + c.schedule_id + '">' +
                                '<button class="button button-small button-outline">Enroll Now</button>' +
                              '</form>'
                        );

                    grid.appendChild(card);
                });
            })
            .catch(function () {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: red;">Failed to load classes.</p>';
            });
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    var debounceTimer;
    function debouncedFetch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchClasses, 300);
    }

    searchInput.addEventListener('input', debouncedFetch);
    trainerSelect.addEventListener('change', fetchClasses);
    dateInput.addEventListener('change', fetchClasses);

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            fetchClasses();
        });
    }
});
