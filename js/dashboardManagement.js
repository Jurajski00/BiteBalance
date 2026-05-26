const entryModal = new bootstrap.Modal(document.getElementById('modalAddEntry'));
const monthViewModal = new bootstrap.Modal(document.getElementById('modalMonthView'));

let activeSelectedDate = '';
let monthViewPivotDate = new Date();
let itemsCacheSnapshot = { products: [], dishes: [] };

const getLocalIsoDateString = (dateObj) => {
    const y = dateObj.getFullYear();
    const m = String(dateObj.getMonth() + 1).padStart(2, '0');
    const d = String(dateObj.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

const realTodayIso = getLocalIsoDateString(new Date());

const inputSearch = document.getElementById('inputItemSearch');
const autocompleteWrapper = document.getElementById('searchAutocompleteWrapper');
const hiddenItemId = document.getElementById('selectEntryItem');
const selectionFeedback = document.getElementById('divSelectionFeedback');
const selectedLabel = document.getElementById('txtSelectedLabel');

function initCalendarNavigation() {
    const navContainer = document.getElementById('weekCalNav');
    if (!navContainer) return;

    if (!activeSelectedDate) {
        activeSelectedDate = realTodayIso;
    }

    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const focusedDate = new Date(activeSelectedDate);
    const dayOfWeekIdx = focusedDate.getDay();
    const distanceToMonday = (dayOfWeekIdx === 0) ? -6 : 1 - dayOfWeekIdx;
    
    const mondayPivot = new Date(focusedDate);
    mondayPivot.setDate(focusedDate.getDate() + distanceToMonday);

    let html = '';
    for (let i = 0; i < 7; i++) {
        const loopDate = new Date(mondayPivot);
        loopDate.setDate(mondayPivot.getDate() + i);

        const isoStr = getLocalIsoDateString(loopDate);
        const displayDay = String(loopDate.getDate()).padStart(2, '0');
        const displayMonth = String(loopDate.getMonth() + 1).padStart(2, '0');
        
        let labelString = `${daysOfWeek[loopDate.getDay()]} ${displayDay}.${displayMonth}`;
        const isTodayClass = (isoStr === realTodayIso) ? 'border border-success bg-success bg-opacity-10 rounded px-1' : '';

        html += `
            <li class="nav-item">
                <a class="nav-link link-success date-nav-trigger ${isTodayClass}" id="cal-${isoStr}" href="#" data-date="${isoStr}" onclick="switchActiveDate('${isoStr}')">
                    ${labelString}
                </a>
            </li>
        `;
    }
    
    navContainer.innerHTML = html;
    updateSelectedCalendarUiHighlight();
}

function switchActiveDate(isoStringDate) {
    activeSelectedDate = isoStringDate;
    initCalendarNavigation();
    loadDashboardEntries();
}

function updateSelectedCalendarUiHighlight() {
    document.querySelectorAll('.date-nav-trigger').forEach(el => {
        if (el.getAttribute('data-date') === activeSelectedDate) {
            el.classList.add('active', 'fw-bold');
        } else {
            el.classList.remove('active', 'fw-bold');
        }
    });
    document.getElementById('txtSummaryDate').textContent = activeSelectedDate === realTodayIso ? 'Today' : activeSelectedDate;
}

async function loadDashboardEntries() {
    const meals = ['breakfast', 'lunch', 'dinner'];

    try {
        const response = await fetch(`dashboardApi.php?action=getEntries&date=${activeSelectedDate}`);
        const result = await response.json();

        if (result.success) {
            document.getElementById('totCalories').textContent = Math.round(result.totals.calories);
            document.getElementById('totProtein').textContent = parseFloat(result.totals.protein).toFixed(1) + 'g';
            document.getElementById('totFat').textContent = parseFloat(result.totals.fat).toFixed(1) + 'g';
            document.getElementById('totCarbs').textContent = parseFloat(result.totals.carbohydrates).toFixed(1) + 'g';

            meals.forEach(meal => {
                const list = result.data.filter(x => x.meal_type === meal);
                const tbody = document.getElementById(`mealTableBody-${meal}`);
                
                if (!tbody) return;

                if (list.length === 0) {
                    tbody.innerHTML = `<tr><td class="text-center py-2 text-muted border-0 opacity-50">No items logged for this meal.</td></tr>`;
                    return;
                }

                let html = '';
                list.forEach(e => {
                    const itemName = e.id_product ? e.product_name : e.dish_name;
                    const badgeHtml = e.id_product 
                        ? `<span class="badge bg-light text-secondary border">Product</span>` 
                        : `<span class="badge bg-light text-primary border border-primary-subtle">Recipe</span>`;

                    html += `
                        <tr>
                            <td class="ps-4 fw-bold text-dark" style="font-size:1rem;">${itemName} ${badgeHtml}</td>
                            <td style="width:110px;"><span class="fw-bold text-dark">${e.weight}</span> <small class="text-muted">g</small></td>
                            <td style="width:130px;"><span class="fw-bold text-success">🔥 ${Math.round(e.calculated_kcal)}</span> <small class="text-muted">kcal</small></td>
                            <td class="d-none d-md-table-cell">
                                <span class="text-primary">P: ${parseFloat(e.calculated_protein || 0).toFixed(1)}g</span> · 
                                <span class="text-danger">F: ${parseFloat(e.calculated_fat || 0).toFixed(1)}g</span> · 
                                <span class="text-warning-emphasis">C: ${parseFloat(e.calculated_carbs || 0).toFixed(1)}g</span>
                            </td>
                            <td class="text-end pe-4" style="width:60px;">
                                <button type="button" class="btn btn-link link-danger p-0 border-0" onclick="deleteEntry(${e.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            });
        }
    } catch (err) {
        console.error(err);
    }
}

function renderMonthCalendarGrid() {
    const container = document.getElementById('monthGridCellsContainer');
    const titleEl = document.getElementById('txtMonthViewTitle');
    if (!container || !titleEl) return;

    const monthsLabels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    titleEl.textContent = `${monthsLabels[monthViewPivotDate.getMonth()]} ${monthViewPivotDate.getFullYear()}`;

    container.innerHTML = '';

    const currentYear = monthViewPivotDate.getFullYear();
    const currentMonth = monthViewPivotDate.getMonth();

    const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
    let startOffset = firstDayOfMonth.getDay(); 
    let distanceToPrevMonday = (startOffset === 0) ? -6 : 1 - startOffset;

    const gridStartDate = new Date(firstDayOfMonth);
    gridStartDate.setDate(firstDayOfMonth.getDate() + distanceToPrevMonday);

    for (let i = 0; i < 42; i++) {
        const cellDate = new Date(gridStartDate);
        cellDate.setDate(gridStartDate.getDate() + i);

        const cellIso = getLocalIsoDateString(cellDate);
        const cellButton = document.createElement('div');
        cellButton.className = 'calendar-day-cell';
        
        if (cellDate.getMonth() !== currentMonth) {
            cellButton.classList.add('other-month');
        }
        if (cellIso === activeSelectedDate) {
            cellButton.classList.add('active-selected');
        }
        if (cellIso === realTodayIso) {
            cellButton.classList.add('real-today');
        }

        cellButton.innerHTML = `<span class="fw-bold small mb-0">${cellDate.getDate()}</span>`;
        
        cellButton.addEventListener('click', () => {
            monthViewModal.hide();
            switchActiveDate(cellIso);
        });

        container.appendChild(cellButton);
    }
}

document.getElementById('btnPrevMonth').addEventListener('click', () => {
    monthViewPivotDate.setMonth(monthViewPivotDate.getMonth() - 1);
    renderMonthCalendarGrid();
});

document.getElementById('btnNextMonth').addEventListener('click', () => {
    monthViewPivotDate.setMonth(monthViewPivotDate.getMonth() + 1);
    renderMonthCalendarGrid();
});

document.getElementById('btnOpenMonthView').addEventListener('click', () => {
    monthViewPivotDate = new Date(activeSelectedDate);
    renderMonthCalendarGrid();
    monthViewModal.show();
});

async function openAddEntryModal(mealType) {
    document.getElementById('inputMealType').value = mealType;
    document.getElementById('entryModalTitle').textContent = `Log item to ${mealType.charAt(0).toUpperCase() + mealType.slice(1)}`;
    document.getElementById('entryWeight').value = 100;
    
    document.getElementById('typeProduct').checked = true;
    
    inputSearch.value = '';
    hiddenItemId.value = '';
    autocompleteWrapper.classList.add('d-none');
    autocompleteWrapper.innerHTML = '';
    selectionFeedback.classList.add('d-none');
    
    updateSearchPlaceholderText();
    entryModal.show();
}

function updateSearchPlaceholderText() {
    const isProduct = document.getElementById('typeProduct').checked;
    const label = document.getElementById('lblSelectTarget');
    
    inputSearch.value = '';
    hiddenItemId.value = '';
    autocompleteWrapper.classList.add('d-none');
    selectionFeedback.classList.add('d-none');

    if (isProduct) {
        label.textContent = "Search Product Item *";
        inputSearch.placeholder = "Type ingredient name (e.g. Chicken)...";
    } else {
        label.textContent = "Search Custom Recipe Dish *";
        inputSearch.placeholder = "Type recipe dish name (e.g. Salad)...";
    }
}

function executeAutocompleteSearchFilter() {
    const query = inputSearch.value.trim().toLowerCase();
    const isProduct = document.getElementById('typeProduct').checked;
    
    if (query.length === 0) {
        autocompleteWrapper.classList.add('d-none');
        autocompleteWrapper.innerHTML = '';
        return;
    }

    const dataset = isProduct ? itemsCacheSnapshot.products : itemsCacheSnapshot.dishes;
    if (!dataset) return;

    const filteredResults = dataset.filter(item => item && item.name && item.name.toLowerCase().includes(query));

    if (filteredResults.length === 0) {
        autocompleteWrapper.innerHTML = `<div class="list-group-item list-group-item-action text-muted disabled small bg-white py-2">No items found matching filter criteria</div>`;
        autocompleteWrapper.classList.remove('d-none');
        return;
    }

    let html = '';
    filteredResults.forEach(item => {
        const secondaryMetricLabel = isProduct 
            ? `${item.energy} kcal / 100g` 
            : `~${Math.round(item.total_kcal)} kcal total`;

        html += `
            <button type="button" class="list-group-item list-group-item-action bg-white text-start d-flex justify-content-between align-items-center py-2 border-bottom-1" 
                    onclick="selectAutocompleteItem('${item.id}', '${item.name.replace(/'/g, "\\'")}', '${secondaryMetricLabel}')">
                <div>
                    <span class="fw-bold text-dark d-block text-truncate" style="max-width: 280px;">${item.name}</span>
                </div>
                <span class="badge bg-light text-success border small">${secondaryMetricLabel}</span>
            </button>
        `;
    });

    autocompleteWrapper.innerHTML = html;
    autocompleteWrapper.classList.remove('d-none');
}

function selectAutocompleteItem(id, name, description) {
    hiddenItemId.value = id;
    inputSearch.value = name;
    
    selectedLabel.textContent = `${name} (${description})`;
    selectionFeedback.classList.remove('d-none');
    
    autocompleteWrapper.classList.add('d-none');
}

if (inputSearch) {
    inputSearch.addEventListener('input', executeAutocompleteSearchFilter);
}

document.addEventListener('click', (e) => {
    if (inputSearch && autocompleteWrapper && !inputSearch.contains(e.target) && !autocompleteWrapper.contains(e.target)) {
        autocompleteWrapper.classList.add('d-none');
    }
});

document.querySelectorAll('input[name="entryType"]').forEach(radio => {
    radio.addEventListener('change', updateSearchPlaceholderText);
});

async function fetchLookupCatalogs() {
    try {
        const response = await fetch('dashboardApi.php?action=getLookupCatalogs');
        const result = await response.json();
        if (result.success) {
            itemsCacheSnapshot.products = result.products || [];
            itemsCacheSnapshot.dishes = result.dishes || [];
        }
    } catch (err) {
        console.error(err);
    }
}

document.getElementById('btnSaveEntry').addEventListener('click', async () => {
    const mealType = document.getElementById('inputMealType').value;
    const isProduct = document.getElementById('typeProduct').checked;
    const itemId = hiddenItemId.value;
    const weight = document.getElementById('entryWeight').value;

    if (!itemId || weight <= 0) {
        alert('Please select a valid item mapping target reference and state custom portion size.');
        return;
    }

    const fd = new FormData();
    fd.append('action', 'addEntry');
    fd.append('date', activeSelectedDate);
    fd.append('meal_type', mealType);
    fd.append('weight', weight);
    fd.append(isProduct ? 'id_product' : 'id_dish', itemId);

    try {
        const response = await fetch('dashboardApi.php', { method: 'POST', body: fd });
        const result = await response.json();

        if (result.success) {
            entryModal.hide();
            loadDashboardEntries();
        } else {
            alert(result.message);
        }
    } catch (err) {
        console.error(err);
    }
});

async function deleteEntry(id) {
    if (!confirm('Remove this food logging item entry row?')) return;

    const fd = new FormData();
    fd.append('action', 'deleteEntry');
    fd.append('id', id);

    try {
        const response = await fetch('dashboardApi.php', { method: 'POST', body: fd });
        const result = await response.json();
        if (result.success) {
            loadDashboardEntries();
        }
    } catch (err) {
        console.error(err);
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    initCalendarNavigation();
    await fetchLookupCatalogs();
    loadDashboardEntries();
});