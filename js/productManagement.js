// ---PRODUCT MODAL---
const productModal = new bootstrap.Modal(document.querySelector('#productModal'));
productModal.hide();

function openProductModal(id = null) {
    document.getElementById('productModalTitle').textContent = id ? 'Edit Product' : 'Add Product';
    document.getElementById('productId').value = id || '';
    document.getElementById('productName').value = '';
    document.getElementById('productType').value = '';
    document.getElementById('productEnergy').value = 0;
    document.getElementById('productProtein').value = 0;
    document.getElementById('productFat').value = 0;
    document.getElementById('productCarbs').value = 0;
    document.getElementById('productPhoto').value = '';
    document.getElementById('currentPhotoPreview').classList.add('d-none');

    if (id) {
        const p = currentProducts.find(x => x.id == id);
        if (p) {
            document.getElementById('productName').value = p.name;
            document.getElementById('productType').value = p.id_type;
            document.getElementById('productEnergy').value = p.energy;
            document.getElementById('productProtein').value = p.protein;
            document.getElementById('productFat').value = p.fat;
            document.getElementById('productCarbs').value = p.carbohydrates;
            if (p.photo) {
                document.getElementById('currentPhotoImg').src = `uploads/photos/${p.photo}`;
                document.getElementById('currentPhotoPreview').classList.remove('d-none');
            }
        }
    }
    productModal.show();
}

const buttonModal = document.querySelector("#buttonModal");
if (buttonModal) {
    buttonModal.addEventListener('click', () => {
        openProductModal(); 
    });
}

// ---LOAD TYPES---
async function loadTypes() {
    const typeSelect = document.getElementById('productType');
    if (!typeSelect) return;

    try {
        const response = await fetch('productApi.php?action=getTypes');
        const result = await response.json();

        if (result.success) {
            let html = '<option value="" disabled selected>-- Select Category --</option>';
            
            result.data.forEach(type => {
                html += `<option value="${type.id}">${type.name}</option>`;
            });

            typeSelect.innerHTML = html;
        } else {
            console.error('API Error:', result.message);
        }
    } catch (error) {
        console.error('Infrastructure Error fetching types:', error);
    }
}

let currentProducts = [];
// ---LOAD PRODUCTS---
async function loadProducts() {
    const tbody = document.getElementById('productsTableBody');
    if (!tbody) return;

    try {
        const response = await fetch('productApi.php?action=getProducts');
        const result = await response.json();

        if (result.success) {
            currentProducts = result.data;
            
            if (currentProducts.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No products found. Add your first item!</td></tr>`;
                return;
            }

            let html = '';
            currentProducts.forEach(p => {
                const photoHtml = p.photo 
                    ? `<img src="uploads/photos/${p.photo}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">`
                    : `<div class="bg-light text-muted d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px;"><i class="bi bi-egg-fried"></i></div>`;

                html += `
                    <tr>
                        <td>${photoHtml}</td>
                        <td><strong class="text-dark">${p.name}</strong></td>
                        <td><span class="badge bg-light text-success border border-success-subtle">${p.type_name || 'Unassigned'}</span></td>
                        <td><span class="fw-bold">${p.energy}</span> <small class="text-muted">kcal</small></td>
                        <td>
                            <span class="text-primary">P: ${p.protein}g</span> · 
                            <span class="text-danger">F: ${p.fat}g</span> · 
                            <span class="text-warning-emphasis">C: ${p.carbohydrates}g</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="openProductModal(${p.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${p.id})">
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
        console.error('Error fetching products:', error);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Infrastructure loss during loading loop.</td></tr>`;
    }
}

// ---DELETE PRODUCT---
async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product? This will remove it from your selection menu.')) {
        return; 
    }

    const fd = new FormData();
    fd.append('action', 'deleteProduct');
    fd.append('id', id);

    try {
        const response = await fetch('productApi.php', {
            method: 'POST',
            body: fd
        });
        const result = await response.json();

        if (result.success) {
            loadProducts();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert('An infrastructure error occurred while trying to remove the product.');
    }
}

// ---SAVE PRODUCT---
document.getElementById('btnSaveProduct').addEventListener('click', async () => {
    const id = document.getElementById('productId').value;
    
    const saveProduct = new FormData();
    saveProduct.append('action', id ? 'editProduct' : 'addProduct');
    if (id) saveProduct.append('id', id);

    saveProduct.append('name', document.getElementById('productName').value.trim());
    saveProduct.append('id_type', document.getElementById('productType').value);
    saveProduct.append('energy', document.getElementById('productEnergy').value);
    saveProduct.append('protein', document.getElementById('productProtein').value);
    saveProduct.append('fat', document.getElementById('productFat').value);
    saveProduct.append('carbohydrates', document.getElementById('productCarbs').value);

    const photoInput = document.getElementById('productPhoto');
    if (photoInput.files.length > 0) {
        saveProduct.append('photo', photoInput.files[0]);
    }

    if (!document.getElementById('productName').value.trim() || !document.getElementById('productType').value) {
        alert('Please fill out all required fields (*)');
        return;
    }

    try {
        const response = await fetch('productApi.php', {
            method: 'POST',
            body: saveProduct
        });

        const data = await response.json();

        if (data.success) {
            productModal.hide();
            
            if (typeof loadProducts === 'function') {
                loadProducts(); 
            } else {
                location.reload();
            }
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An infrastructure error occurred while saving the product.');
    }
});

// ---ON PAGE LOAD---
document.addEventListener('DOMContentLoaded', () => {
    loadTypes(); 
    
    if (typeof loadProducts === 'function') {
        loadProducts();
    }
});