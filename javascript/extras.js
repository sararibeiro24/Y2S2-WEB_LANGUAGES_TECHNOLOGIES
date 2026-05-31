
document.addEventListener('DOMContentLoaded', () => {
    const debounce = (fn, delay = 300) => {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    };

    const equipmentSearchInput = document.getElementById('equipmentSearchInput');
    const equipmentResults = document.getElementById('equipmentResults');
    const equipmentStatusFilter = document.getElementById('equipmentStatusFilter');

    const nutritionSearchInput = document.getElementById('nutritionSearchInput');
    const nutritionResults = document.getElementById('nutritionResults');
    const nutritionGoalFilter = document.getElementById('nutritionGoalFilter');
    const nutritionTrainerFilter = document.getElementById('nutritionTrainerFilter');

    const openRequestModalBtn = document.getElementById('openRequestModalBtn');
    const requestPlanModal = document.getElementById('requestPlanModal');
    const closeModalBtn = document.getElementById('closeModalBtn');

    const foodImages = [
        'top-view-chicken-salad-with-chopped-cabbage-colorful-bell-peppers-plate.jpg',
        'grilled-cod-with-vegetables-plate-black-stone-background.jpg',
        'grilled-chicken-skewers-green-salad-menu-recipe-idea.jpg',
        'english-breakfast-dish.jpg',
        'chicken-salad-with-vegetables-olives.jpg',
        'mixed-vegetable-salad-with-colorful-food.jpg',
        'stew.jpg',
        'green_salad.jpg',
        'protein_bowl.jpg',
        'protein_smoothie.jpg',
        'breakfast.png',
        'close-up-traditional-indian-food-with-chicken.jpg',
        'omega3.jpg'
    ];

    const equipImages = [
        'dumbells.jpg',
        'heavy_weights.jpg',
        'leg_press.jpg',
        'cardio.jpg',
        'box_ring.jpg',
        'boxing.jpg',
        'girl_training.jpg',
        'man_training.jpg',
        'class.jpg',
        'dumbells2.jpg',
        'cardio2.jpg'
    ];

    function imgUrl(filename) {
        const u = new URL('../img/' + encodeURIComponent(filename), window.location.origin);
        return u.toString();
    }

    function createFlipCard(frontHtml, backHtml, imageUrl) {
        const hasImage = imageUrl ? ' has-image' : '';
        const style = imageUrl ? ' style="background-image: url(\'' + imageUrl + '\');"' : '';
        return '<article class="flip-card">' +
            '<div class="flip-card-inner">' +
            '<div class="flip-card-front' + hasImage + '"' + style + '>' +
            frontHtml +
            '</div>' +
            '<div class="flip-card-back">' +
            backHtml +
            '</div>' +
            '</div>' +
            '</article>';
    }

    function goalBadgeClass(goal) {
        if (!goal) return '';
        const cls = goal.toLowerCase().replace(/\s+/g, '-');
        return 'nutrition-goal-badge ' + cls;
    }

    function renderEquipment(items) {
        if (!equipmentResults) return;
        if (!items.length) {
            equipmentResults.innerHTML = '<p class="empty-state">No equipment found for this filter.</p>';
            return;
        }

        equipmentResults.innerHTML = items.map(function (item, idx) {
            var availabilityLabel = item.available_quantity > 0 ? 'Available' : 'Unavailable';
            var statusClass = item.available_quantity > 0 ? 'available-equip' : 'unavailable-equip';
            var image = imgUrl(equipImages[idx % equipImages.length]);

            var front = '' +
                '<div class="card-header">' +
                '<h3>' + item.name + '</h3>' +
                '<span class="equipment-status-badge ' + statusClass + '">' + availabilityLabel + '</span>' +
                '</div>' +
                '<div class="equipment-count">' +
                item.available_quantity + ' <span class="total">/ ' + item.total_quantity + '</span>' +
                '</div>' +
                '<p class="card-copy">ready to use</p>';

            var back = '' +
                '<span class="equipment-status-badge ' + statusClass + '">' + availabilityLabel + '</span>' +
                '<div class="equip-detail-row">' +
                '<span class="label">Total units</span>' +
                '<span class="value">' + item.total_quantity + '</span>' +
                '</div>' +
                '<div class="equip-detail-row">' +
                '<span class="label">Available now</span>' +
                '<span class="value">' + item.available_quantity + '</span>' +
                '</div>' +
                '<div class="equip-detail-row">' +
                '<span class="label">Last updated</span>' +
                '<span class="value">' + (item.last_updated || '-') + '</span>' +
                '</div>' +
                '<p class="card-copy" style="margin-top:0.6em;color:rgba(255,255,255,0.7);">Keep your training session smooth with updated availability.</p>';

            return createFlipCard(front, back, image);
        }).join('');
    }

    function renderNutrition(plans) {
        if (!nutritionResults) return;
        if (!plans.length) {
            nutritionResults.innerHTML = '<p class="empty-state">No nutrition plans found for this filter.</p>';
            return;
        }

        nutritionResults.innerHTML = plans.map(function (plan, idx) {
            var image = imgUrl(foodImages[idx % foodImages.length]);

            var front = '' +
                '<div class="card-header">' +
                '<h3>' + plan.goal + '</h3>' +
                '<span class="badge-trainer">' + plan.trainer_name + '</span>' +
                '</div>' +
                '<div class="nutrition-calories">' +
                (plan.target_calories || 'Custom') + ' <small>kcal</small>' +
                '</div>' +
                '<p class="nutrition-member">for <strong>' + plan.member_name + '</strong></p>';

            var back = '' +
                '<span class="' + goalBadgeClass(plan.goal) + '">' + (plan.goal || 'Plan') + '</span>' +
                '<div class="meal-detail">' + (plan.meal_details || 'Personalized nutrition guidance tailored to your goal.') + '</div>' +
                '<p class="nutrition-date">Assigned ' + (plan.created_at || '-') + '</p>';

            return createFlipCard(front, back, image);
        }).join('');
    }

    async function fetchEquipment() {
        if (!equipmentResults) return;
        const query = equipmentSearchInput?.value.trim() || '';
        const status = equipmentStatusFilter?.value || '';
        const url = new URL('../actions/action_get_equipment.php', window.location.origin);
        if (query) url.searchParams.set('query', query);
        if (status) url.searchParams.set('status', status);

        try {
            const response = await fetch(url.toString());
            const data = await response.json();
            renderEquipment(data);
        } catch (error) {
            console.error('Failed to load equipment:', error);
        }
    }

    async function fetchNutrition() {
        if (!nutritionResults) return;
        const query = nutritionSearchInput?.value.trim() || '';
        const goal = nutritionGoalFilter?.value || '';
        const trainer = nutritionTrainerFilter?.value || '';
        const url = new URL('../actions/action_get_nutrition.php', window.location.origin);
        if (query) url.searchParams.set('query', query);
        if (goal) url.searchParams.set('goal', goal);
        if (trainer) url.searchParams.set('trainer', trainer);

        try {
            const response = await fetch(url.toString());
            const data = await response.json();
            renderNutrition(data);
        } catch (error) {
            console.error('Failed to load nutrition plans:', error);
        }
    }

    if (equipmentSearchInput && equipmentResults) {
        equipmentSearchInput.addEventListener('input', debounce(fetchEquipment));
        equipmentStatusFilter?.addEventListener('change', fetchEquipment);
        fetchEquipment();
    }

    if (nutritionSearchInput && nutritionResults) {
        nutritionSearchInput.addEventListener('input', debounce(fetchNutrition));
        nutritionGoalFilter?.addEventListener('change', fetchNutrition);
        nutritionTrainerFilter?.addEventListener('change', fetchNutrition);
        fetchNutrition();
    }

    if (openRequestModalBtn && requestPlanModal) {
        openRequestModalBtn.addEventListener('click', () => {
            requestPlanModal.style.display = 'flex';
        });

        closeModalBtn.addEventListener('click', () => {
            requestPlanModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === requestPlanModal) {
                requestPlanModal.style.display = 'none';
            }
        });
    }

    const feedbackBtn = document.querySelector('.feedback-trigger');
    const feedbackModal = document.getElementById('feedbackModal');
    const closeFeedbackBtn = document.getElementById('closeFeedbackBtn');

    if (feedbackBtn && feedbackModal) {
        feedbackBtn.addEventListener('click', (e) => {
            e.preventDefault();
            feedbackModal.style.display = 'flex';
        });

        closeFeedbackBtn?.addEventListener('click', () => {
            feedbackModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === feedbackModal) {
                feedbackModal.style.display = 'none';
            }
        });
    }
    
});