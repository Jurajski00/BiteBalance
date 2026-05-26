const dishModal = new bootstrap.Modal(document.getElementById('modalCreateDish'));
let trackingIngredientsMap = [];
let currentDishes = [];

const recipeSearchInput = document.getElementById('recipeProductSearch');
const searchDropdownOutput = document.getElementById('recipeSearchDropdown');
const tableBodyNode = document.getElementById('recipeIngredientsTableBody');
const inputSearchDishes = document.getElementById('inputSearchDishes');

// --- LOAD DISHES FROM DB ---
async function loadDishes() {
    const tbody = document.getElementById('dishesTableBody');
    if (!tbody) return;

    try {
        const response = await fetch('dishApi.php?action=getDishes');
        const result = await response.json();

        if (result.success) {
            currentDishes = result.data;
            
            if (currentDishes.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No custom recipes built yet. Add your first dish!</td></tr>`;
                return;
            }

            let html = '';
            currentDishes.forEach(d => {
                const photoHtml = d.photo 
                    ? `<img src="uploads/photos/${d.photo}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">`
                    : `<div class="bg-light text-muted d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px;"><i class="bi bi-egg-fried"></i></div>`;

                html += `
                    <tr class="dish-row-item" data-name="${d.name.toLowerCase()}">
                        <td>${photoHtml}</td>
                        <td><strong class="text-dark">${d.name}</strong></td>
                        <td><span class="text-muted small">${d.description || 'No description'}</span></td>
                        <td><span class="fw-bold text-success">🔥 ${Math.round(d.total_kcal)}</span> <small class="text-muted">kcal</small></td>
                        <td>
                            <span class="text-primary">P: ${parseFloat(d.total_protein).toFixed(1)}g</span> · 
                            <span class="text-danger">F: ${parseFloat(d.total_fat).toFixed(1)}g</span> · 
                            <span class="text-warning-emphasis">C: ${parseFloat(d.total_carbs).toFixed(1)}g</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="openDishModal(${d.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDish(${d.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Error: ${result.message}</td></tr>`;
        }
    } catch (error) {
        console.error('Error fetching dishes:', error);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Infrastructure loss during loading loop.</td></tr>`;
    }
}

// --- DISH MODAL CONTROLLER INITIALIZER ---
async function openDishModal(id = null) {
    document.getElementById('inputDishId').value = id || '';
    document.getElementById('dishName').value = '';
    document.getElementById('dishDescription').value = '';
    document.getElementById('dishPhoto').value = '';
    document.getElementById('dishModalTitle').textContent = id ? 'Edit Custom Dish' : 'Assemble Custom Dish';
    document.getElementById('currentPhotoPreview').classList.add('d-none');
    
    trackingIngredientsMap = [];
    regenerateUIIngredientsGridTable();

    if (id) {
        const d = currentDishes.find(x => x.id == id);
        if (d) {
            document.getElementById('dishName').value = d.name;
            document.getElementById('dishDescription').value = d.description || '';
            if (d.photo) {
                document.getElementById('currentPhotoImg').src = `uploads/photos/${d.photo}`;
                document.getElementById('currentPhotoPreview').classList.remove('d-none');
            }

            try {
                const response = await fetch(`dishApi.php?action=getDishIngredients&dish_id=${id}`);
                const result = await response.json();
                if (result.success) {
                    trackingIngredientsMap = result.data.map(ing => ({
                        id: ing.id_product,
                        name: ing.name,
                        energy: ing.energy,
                        weight: ing.weight
                    }));
                    regenerateUIIngredientsGridTable();
                }
            } catch (err) {
                console.error("Failed to fetch ingredients mapping array:", err);
            }
        }
    }
    dishModal.show();
}

// --- AUTOCOMPLETE SUGGESTION ENGINE LOOP ---
recipeSearchInput.addEventListener('input', async function() {
    const term = this.value.trim();
    if (term.length < 1) {
        searchDropdownOutput.classList.add('d-none');
        return;
    }

    try {
        const response = await fetch(`dishApi.php?action=searchProducts&term=${encodeURIComponent(term)}`);
        const products = await response.json();

        searchDropdownOutput.innerHTML = '';
        if (products.length === 0) {
            searchDropdownOutput.innerHTML = '<div class="list-group-item small text-muted">No catalog matching products found.</div>';
            searchDropdownOutput.classList.remove('d-none');
            return;
        }

        products.forEach(p => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action small d-flex justify-content-between align-items-center';
            btn.innerHTML = `<strong>${p.name}</strong> <span class="text-muted text-end small">${p.energy} kcal/100g</span>`;
            
            btn.addEventListener('click', () => {
                const duplicated = trackingIngredientsMap.some(x => parseInt(x.id) === parseInt(p.id));
                if (duplicated) {
                    alert('This product ingredient is already added to your dish builder array.');
                    return;
                }

                trackingIngredientsMap.push({
                    id: p.id,
                    name: p.name,
                    energy: p.energy,
                    weight: 100
                });

                searchDropdownOutput.classList.add('d-none');
                recipeSearchInput.value = '';
                regenerateUIIngredientsGridTable();
            });
            searchDropdownOutput.appendChild(btn);
        });
        searchDropdownOutput.classList.remove('d-none');
    } catch (err) {
        console.error(err);
    }
});

// --- RENDER INGREDIENT CONTEXT TABLE MATRIX ---
function regenerateUIIngredientsGridTable() {
    tableBodyNode.innerHTML = '';

    if (trackingIngredientsMap.length === 0) {
        tableBodyNode.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-muted small">No ingredients added yet. Search above to begin adding components.</td></tr>`;
        return;
    }

    trackingIngredientsMap.forEach((ing, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="fw-bold text-dark">${ing.name}</td>
            <td><span class="badge bg-light text-secondary border">${ing.energy} kcal/100g</span></td>
            <td>
                <div class="input-group input-group-sm" style="width: 120px;">
                    <input type="number" class="form-control" value="${ing.weight}" min="1" oninput="updateWeight(${idx}, this.value)">
                    <span class="input-group-text bg-light text-muted">g</span>
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-0" onclick="removeIngredient(${idx})">
                    <i class="bi bi-dash-circle-fill fs-5"></i>
                </button>
            </td>
        `;
        tableBodyNode.appendChild(tr);
    });
}

function updateWeight(idx, value) {
    trackingIngredientsMap[idx].weight = parseFloat(value) || 0;
}

function removeIngredient(idx) {
    trackingIngredientsMap.splice(idx, 1);
    regenerateUIIngredientsGridTable();
}

// --- SAVE DISH SUBMISSION LISTENER ---
document.getElementById('btnSaveDish').addEventListener('click', async () => {
    const id = document.getElementById('inputDishId').value;
    const name = document.getElementById('dishName').value.trim();
    
    if (!name || trackingIngredientsMap.length === 0) {
        alert('Please provide a valid Dish Name and add at least one ingredient.');
        return;
    }

    const fd = new FormData();
    fd.append('action', id ? 'editDish' : 'addDish');
    if (id) fd.append('id', id);
    
    fd.append('name', name);
    fd.append('description', document.getElementById('dishDescription').value.trim());
    fd.append('ingredients', JSON.stringify(trackingIngredientsMap));

    const photoInput = document.getElementById('dishPhoto');
    if (photoInput.files.length > 0) {
        fd.append('photo', photoInput.files[0]);
    }

    try {
        const response = await fetch('dishApi.php', { method: 'POST', body: fd });
        const result = await response.json();

        if (result.success) {
            dishModal.hide();
            loadDishes();
        } else {
            alert(result.message);
        }
    } catch (err) {
        console.error(err);
        alert('An infrastructure error occurred while saving the recipe.');
    }
});

// --- DISH PURGE DELETE ROUTINE ---
async function deleteDish(id) {
    if (!confirm('Are you sure you want to delete this custom recipe dish?')) return;

    const fd = new FormData();
    fd.append('action', 'deleteDish');
    fd.append('id', id);

    try {
        const response = await fetch('dishApi.php', { method: 'POST', body: fd });
        const result = await response.json();

        if (result.success) {
            loadDishes();
        } else {
            alert(result.message);
        }
    } catch (err) {
        console.error(err);
    }
}

// --- SEARCH BOX CLIENT FILTER CONTROLLER ---
if (inputSearchDishes) {
    inputSearchDishes.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        document.querySelectorAll('.dish-row-item').forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.display = name.includes(query) ? '' : 'none';
        });
    });
}

// --- GLOBAL CLICK DISMISS ENGINE ---
document.addEventListener('click', (e) => {
    if (recipeSearchInput && !recipeSearchInput.contains(e.target) && !searchDropdownOutput.contains(e.target)) {
        searchDropdownOutput.classList.add('d-none');
    }
});

const btnOpenCreateModal = document.getElementById('btnOpenCreateModal');
if (btnOpenCreateModal) {
    btnOpenCreateModal.addEventListener('click', () => openDishModal());
}

// --- BOOTSTRAP INITIALIZATION LOOP ROUTINE ---
document.addEventListener('DOMContentLoaded', () => {
    loadDishes();
});