document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('trainerSearch');
    var grid = document.getElementById('trainerGrid');

    if (!searchInput || !grid) return;

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
                    var div = document.createElement('div');
                    div.className = 'team-member';

                    var initials = t.name.split(' ').map(function (s) { return s[0]; }).join('').toUpperCase().substring(0, 2);
                    var specsHtml = '';
                    if (t.specializations_list && t.specializations_list.length > 1) {
                        specsHtml = '<div class="trainer-specs">';
                        t.specializations_list.forEach(function (s) {
                            specsHtml += '<span class="trainer-spec-tag">' + escapeHtml(s.trim()) + '</span>';
                        });
                        specsHtml += '</div>';
                    }

                    div.innerHTML =
                        (t.profile_photo
                            ? '<img src="../img/' + escapeHtml(t.profile_photo) + '" alt="' + escapeHtml(t.name) + '" class="team-member-image">'
                            : '<div class="team-member-image trainer-placeholder">' + initials + '</div>'
                        ) +
                        '<div class="team-member-info">' +
                            '<h3 class="team-member-name">' + escapeHtml(t.name) + '</h3>' +
                            '<p class="team-member-role">' + (t.specializations_list && t.specializations_list[0] ? escapeHtml(t.specializations_list[0]) : 'Fitness') + '</p>' +
                            (t.bio ? '<p class="team-member-description">' + escapeHtml(t.bio) + '</p>' : '') +
                            specsHtml +
                        '</div>';

                    grid.appendChild(div);
                });
            })
            .catch(function () {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: red;">Failed to load trainers.</p>';
            });
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    var debounceTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchTrainers, 300);
    });
});
