<?php
    $accessType = 'private';
    require 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Data:,">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Diet tracker</title>
</head>
<body>

<header class="container-fluid border-bottom px-4 py-2">
    <div class="row align-items-center">
        <div class="col-auto">
            <a class="navbar-brand fs-3 fw-bold text-success" href="index.php">🍎 Diet tracker</a>
        </div>

        <div class="col-auto ms-auto">
            <a type="button" href="products.php" class="btn btn-outline-success">Add products</a>
        </div>
        
        <div class="col-auto">
            <a type="button" href="dishes.php" class="btn btn-outline-success">Add dishes</a>
        </div>

        <div class="col-auto">
            <a type="button" href="account.php" class="btn btn-success">My account</a>
        </div>
    </div>
</header>

<main>
    <div class="container px-5 py-5">
        <div class="row align-items-center mb-4">
            <div class="col-auto">
                <h2 class="fw-bold mb-0 text-success">Products</h2>
            </div>
            <div class="col-auto ms-auto">
                <button type="button" id="buttonModal" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Add product</button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="input-group shadow-sm rounded">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchBar" class="form-control border-start-0" placeholder="Search by product name...">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filterType" class="form-select shadow-sm rounded">
                    <option value="">All Categories</option>
                    </select>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive bg-white shadow-sm rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-success text-success">
                            <tr>
                                <th style="width: 80px;">Photo</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Energy</th>
                                <th>Macros (P / F / C)</th>
                                <th class="text-end" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Loading products...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<<!--  PRODUCT MODAL -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="productModalTitle">Add Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="productId">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Name *</label>
                        <input type="text" id="productName" class="form-control" placeholder="e.g. Chicken breast">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Type *</label>
                        <select id="productType" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Energy (kcal/100g)</label>
                        <input type="number" id="productEnergy" class="form-control" min="0" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Protein (g/100g)</label>
                        <input type="number" id="productProtein" class="form-control" min="0" step="0.1" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fat (g/100g)</label>
                        <input type="number" id="productFat" class="form-control" min="0" step="0.1" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Carbohydrates (g/100g)</label>
                        <input type="number" id="productCarbs" class="form-control" min="0" step="0.1" value="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Photo</label>
                        <input type="file" id="productPhoto" class="form-control" accept="image/*">
                        <div id="currentPhotoPreview" class="mt-2 d-none">
                            <img id="currentPhotoImg" src="" class="img-thumbnail" style="max-height:100px">
                            <span class="ms-2 text-muted small">Current photo</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" id="btnSaveProduct">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="js/productManagement.js"></script>
</body>
</html>