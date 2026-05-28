document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('trainerSearch') && document.getElementById('trainerGrid')) {
        initTrainersPage();
    }
});

function initTrainersPage() {
    const searchInput = document.getElementById('trainerSearch');
    const grid = document.getElementById('trainerGrid');
    const modal = document.getElementById('trainerModal');
    const modalBody = document.getElementById('trainerModalBody');
    
    let debounceTimer;

    grid.addEventListener('click', (e) => {
        const cardWrapper = e.target.closest('#trainerGrid > div');
        
        if (cardWrapper) {
            let trainerId = cardWrapper.dataset.id;
            
            if (!trainerId) {
                const onclickAttr = cardWrapper.getAttribute('onclick');
                if (onclickAttr) {
                    const match = onclickAttr.match(/\d+/);
                    if (match) trainerId = match[0];
                }
            }
            
            if (trainerId) {
                openTrainerModal(trainerId);
            }
        }
    });

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchTrainersLive, 300);
    });

    if (modal) {
        const closeX = modal.querySelector('.modal-close');
        if (closeX) {
            closeX.removeAttribute('onclick');
            closeX.addEventListener('click', () => closeModal('trainerModal'));
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal('trainerModal');
        });
    }

    function openTrainerModal(trainerId) {
        modalBody.innerHTML = '';
        const loading = document.createElement('p');
        loading.style.textAlign = 'center';
        loading.style.color = '#888';
        loading.textContent = 'Loading...';
        modalBody.appendChild(loading);
        
        showModal('trainerModal');

        fetch(`../actions/api_trainer_detail.php?id=${encodeURIComponent(trainerId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    modalBody.innerHTML = '';
                    const errorMsg = document.createElement('p');
                    errorMsg.textContent = 'Trainer not found.';
                    modalBody.appendChild(errorMsg);
                    return;
                }
                renderTrainerModalContent(data);
            })
            .catch(() => {
                modalBody.innerHTML = '';
                const errorMsg = document.createElement('p');
                errorMsg.textContent = 'Failed to load trainer details.';
                modalBody.appendChild(errorMsg);
            });
    }

    function fetchTrainersLive() {
        const params = new URLSearchParams();
        const query = searchInput.value.trim();
        if (query) params.set('search', query);

        fetch(`../actions/api_trainers.php?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                grid.innerHTML = '';
                if (data.length === 0) {
                    const noFound = document.createElement('p');
                    noFound.style.gridColumn = '1/-1';
                    noFound.style.textAlign = 'center';
                    noFound.style.color = '#888';
                    noFound.textContent = 'No trainers found.';
                    grid.appendChild(noFound);
                    return;
                }
                
                data.forEach(trainer => {
                    const wrapper = document.createElement('div');
                    wrapper.dataset.id = trainer.id; 
                    wrapper.appendChild(buildTrainerCardDOM(trainer));
                    grid.appendChild(wrapper);
                });
            })
            .catch(() => {
                grid.innerHTML = '';
                const errorMsg = document.createElement('p');
                errorMsg.style.gridColumn = '1/-1';
                errorMsg.style.textAlign = 'center';
                errorMsg.style.color = 'red';
                errorMsg.textContent = 'Failed to load trainers.';
                grid.appendChild(errorMsg);
            });
    }

    function buildTrainerCardDOM(t) {
        const div = document.createElement('div');
        div.className = 'team-member';

        if (t.profile_photo) {
            const img = document.createElement('img');
            img.src = `../img/${t.profile_photo}`;
            img.alt = t.name;
            img.className = 'team-member-image';
            div.appendChild(img);
        } else {
            const placeholder = document.createElement('div');
            placeholder.className = 'team-member-image trainer-placeholder';
            placeholder.textContent = t.name.split(' ').map(s => s[0]).join('').toUpperCase().substring(0, 2);
            div.appendChild(placeholder);
        }

        const infoDiv = document.createElement('div');
        infoDiv.className = 'team-member-info';

        const h3 = document.createElement('h3');
        h3.className = 'team-member-name';
        h3.textContent = t.name;
        infoDiv.appendChild(h3);

        const role = document.createElement('p');
        role.className = 'team-member-role';
        role.textContent = t.specializations_list && t.specializations_list[0] ? t.specializations_list[0] : 'Fitness';
        infoDiv.appendChild(role);

        if (t.bio) {
            const bio = document.createElement('p');
            bio.className = 'team-member-description';
            bio.textContent = t.bio;
            infoDiv.appendChild(bio);
        }

        if (t.specializations_list && t.specializations_list.length > 1) {
            const specsDiv = document.createElement('div');
            specsDiv.className = 'trainer-specs';
            t.specializations_list.forEach(s => {
                const tag = document.createElement('span');
                tag.className = 'trainer-spec-tag';
                tag.textContent = s.trim();
                specsDiv.appendChild(tag);
            });
            infoDiv.appendChild(specsDiv);
        }

        div.appendChild(infoDiv);
        return div;
    }

    function renderTrainerModalContent(data) {
        modalBody.innerHTML = '';

        if (data.profile_photo) {
            const img = document.createElement('img');
            img.src = `../img/${data.profile_photo}`;
            img.alt = data.name;
            img.className = 'team-member-image';
            img.style.height = '250px';
            modalBody.appendChild(img);
        } else {
            const placeholder = document.createElement('div');
            placeholder.className = 'team-member-image trainer-placeholder';
            placeholder.style.height = '250px';
            placeholder.textContent = getInitials(data.name);
            modalBody.appendChild(placeholder);
        }

        const contentContainer = document.createElement('div');
        contentContainer.style.padding = '1.5em';

        const title = document.createElement('div');
        title.className = 'modal-title';
        title.textContent = data.name;
        contentContainer.appendChild(title);

        if (data.years_experience) {
            const years = document.createElement('div');
            years.style.color = 'var(--ladybug-red)';
            years.style.fontSize = '0.9em';
            years.style.marginBottom = '0.5em';
            years.textContent = `${data.years_experience} years experience`;
            contentContainer.appendChild(years);
        }

        if (data.specializations_list && data.specializations_list.length > 0) {
            const specsContainer = document.createElement('div');
            specsContainer.style.marginBottom = '1em';
            data.specializations_list.forEach(s => {
                const tag = document.createElement('span');
                tag.className = 'trainer-spec-tag';
                tag.style.marginRight = '0.25em';
                tag.textContent = s.trim();
                specsContainer.appendChild(tag);
            });
            contentContainer.appendChild(specsContainer);
        }

        if (data.bio) {
            const bioBody = document.createElement('div');
            bioBody.className = 'modal-body';
            const p = document.createElement('p');
            p.textContent = data.bio;
            bioBody.appendChild(p);
            contentContainer.appendChild(bioBody);
        }

        if (data.certifications) {
            const certBody = document.createElement('div');
            certBody.className = 'modal-body';
            const p = document.createElement('p');
            const strong = document.createElement('strong');
            strong.textContent = 'Certifications: ';
            p.appendChild(strong);
            p.appendChild(document.createTextNode(data.certifications));
            certBody.appendChild(p);
            contentContainer.appendChild(certBody);
        }

        if (data.classes && data.classes.length > 0) {
            const classesDiv = document.createElement('div');
            classesDiv.className = 'modal-trainer-classes';
            const h4 = document.createElement('h4');
            h4.textContent = 'Classes Taught';
            classesDiv.appendChild(h4);

            data.classes.forEach(c => {
                const item = document.createElement('div');
                item.className = 'modal-trainer-class-item';
                const nameSpan = document.createElement('span');
                nameSpan.className = 'tc-name';
                nameSpan.textContent = c.name;
                const countSpan = document.createElement('span');
                countSpan.className = 'tc-count';
                countSpan.textContent = `${c.session_count} sessions`;
                item.append(nameSpan, countSpan);
                classesDiv.appendChild(item);
            });
            contentContainer.appendChild(classesDiv);
        }

        if (data.upcoming_sessions && data.upcoming_sessions.length > 0) {
            const upcomingDiv = document.createElement('div');
            upcomingDiv.className = 'modal-trainer-classes';
            upcomingDiv.style.marginTop = '1em';
            const h4 = document.createElement('h4');
            h4.textContent = 'Upcoming Sessions';
            upcomingDiv.appendChild(h4);

            data.upcoming_sessions.forEach(s => {
                const item = document.createElement('div');
                item.className = 'modal-trainer-class-item';
                const nameSpan = document.createElement('span');
                nameSpan.className = 'tc-name';
                nameSpan.textContent = s.name;

                const d = new Date(s.scheduled_at);
                const dateStr = d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                const time = s.scheduled_at.substring(11, 16);
                
                const timeSpan = document.createElement('span');
                timeSpan.style.color = '#999';
                timeSpan.style.fontSize = '0.85em';
                timeSpan.textContent = `${dateStr} ${time}`;

                item.append(nameSpan, timeSpan);
                upcomingDiv.appendChild(item);
            });
            contentContainer.appendChild(upcomingDiv);
        }

        const footer = document.createElement('div');
        footer.className = 'modal-footer';

        const schedBtn = document.createElement('a');
        schedBtn.href = 'schedule.php';
        schedBtn.className = 'button button-small button-outline';
        schedBtn.textContent = 'View Schedule';

        const closeBtn = document.createElement('button');
        closeBtn.id = 'modalCloseBtn';
        closeBtn.className = 'button button-small button-outline';
        closeBtn.textContent = 'Close';

        footer.append(schedBtn, closeBtn);
        contentContainer.appendChild(footer);
        modalBody.appendChild(contentContainer);

        closeBtn.addEventListener('click', () => closeModal('trainerModal'));
    }
}

function getInitials(name) {
    if (!name) return '?';
    const parts = name.split(' ');
    return (parts[0] ? parts[0][0] : '') + (parts[1] ? parts[1][0] : '');
}

function showModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('active');
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
}