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

    function createFlipCard(frontHtml, backHtml) {
        return `
            <article class="flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front">
                        ${frontHtml}
                    </div>
                    <div class="flip-card-back">
                        ${backHtml}
                    </div>
                </div>
            </article>
        `;
    }

    function renderEquipment(items) {
        if (!equipmentResults) return;
        if (!items.length) {
            equipmentResults.innerHTML = '<p class="empty-state">No equipment found for this filter.</p>';
            return;
        }

        equipmentResults.innerHTML = items.map(item => {
            const availabilityLabel = item.available_quantity > 0 ? 'Available' : 'Unavailable';
            const statusClass = item.available_quantity > 0 ? 'available' : 'unavailable';

            const front = `
                <div class="card-header">
                    <h3>${item.name}</h3>
                    <span class="badge ${statusClass}">${availabilityLabel}</span>
                </div>
                <p class="card-copy">${item.available_quantity}/${item.total_quantity} ready to use.</p>
            `;
            const back = `
                <p><strong>Total units:</strong> ${item.total_quantity}</p>
                <p><strong>Available now:</strong> ${item.available_quantity}</p>
                <p><strong>Last updated:</strong> ${item.last_updated}</p>
                <p class="card-copy">Keep your training session smooth with updated availability.</p>
            `;
            return createFlipCard(front, back);
        }).join('');
    }

    function renderNutrition(plans) {
        if (!nutritionResults) return;
        if (!plans.length) {
            nutritionResults.innerHTML = '<p class="empty-state">No nutrition plans found for this filter.</p>';
            return;
        }

        nutritionResults.innerHTML = plans.map(plan => {
            const front = `
                <div class="card-header">
                    <h3>${plan.goal}</h3>
                    <span class="badge badge-green">${plan.trainer_name}</span>
                </div>
                <p class="card-copy"><strong>Calories:</strong> ${plan.target_calories || 'Custom'}</p>
                <p class="card-copy">For <strong>${plan.member_name}</strong></p>
            `;
            const back = `
                <p><strong>Meal plan:</strong></p>
                <p class="card-copy">${plan.meal_details || 'Personalized nutrition guidance tailored to your goal.'}</p>
                <p><strong>Assigned:</strong> ${plan.created_at}</p>
            `;
            return createFlipCard(front, back);
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
});
