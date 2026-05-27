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
    <title>BiteBalance - Dishes</title>
</head>
<body class="bg-light">

<header class="container-fluid border-bottom px-4 py-2 bg-white">
    <div class="row align-items-center">
        <div class="col-auto">
            <a class="navbar-brand fs-3 fw-bold text-success" href="index.php">🍎 BiteBalance</a>
        </div>
        <div class="col-auto ms-auto">
            <a href="products.php" class="btn btn-outline-success">Add products</a>
        </div>
        <div class="col-auto">
            <a href="dishes.php" class="btn btn-outline-success">Add dishes</a>
        </div>
        <div class="col-auto">
            <a href="account.php" class="btn btn-success">My account</a>
        </div>
    </div>
</header>

<main>
    <div class="container px-5 py-5" style="max-width: 1000px;">
        <div class="row align-items-center mb-4">
            <div class="col-auto">
                <h2 class="fw-bold mb-0 text-success">Custom Dishes & Recipes</h2>
            </div>
            <div class="col-auto ms-auto">
                <button type="button" id="btnOpenCreateModal" class="btn btn-success">
                    <i class="bi bi-plus-lg me-1"></i>Create new dish
                </button>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="inputSearchDishes" class="form-control" placeholder="Search saved dishes by name...">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive bg-white shadow-sm rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-success text-success">
                            <tr>
                                <th style="width: 80px;">Photo</th>
                                <th>Dish Name</th>
                                <th>Description</th>
                                <th>Energy</th>
                                <th>Macros (P / F / C)</th>
                                <th class="text-end" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dishesTableBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Loading custom recipes...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalCreateDish" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="dishModalTitle">Assemble Custom Dish</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="inputDishId">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Dish Name *</label>
                        <input type="text" id="dishName" class="form-control" placeholder="e.g. Homemade Protein Pancakes">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Brief Description</label>
                        <input type="text" id="dishDescription" class="form-control" placeholder="e.g. Makes 4 medium pancakes">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Photo</label>
                        <input type="file" id="dishPhoto" class="form-control" accept="image/*">
                        <div id="currentPhotoPreview" class="mt-2 d-none">
                            <img id="currentPhotoImg" src="" class="img-thumbnail" style="max-height:100px">
                            <span class="ms-2 text-muted small">Current photo</span>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="bi bi-basket3 me-2"></i>Ingredients List Builder</h6>

                <div class="mb-3 position-relative">
                    <label class="form-label fw-bold small text-muted">Search & Add Product Ingredients</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="recipeProductSearch" class="form-control" placeholder="Type here to search system catalog items..." autocomplete="off">
                    </div>
                    <div id="recipeSearchDropdown" class="list-group shadow position-absolute w-100 d-none" style="z-index: 1050; max-height: 200px; overflow-y: auto;"></div>
                </div>

                <div class="table-responsive border rounded p-2 bg-light">
                    <table class="table table-sm table-borderless align-middle mb-0 text-muted">
                        <thead>
                            <tr class="border-bottom text-dark fw-bold">
                                <th>Ingredient Name</th>
                                <th>Base Energy</th>
                                <th style="width: 140px;">Portion Size</th>
                                <th class="text-center" style="width: 50px;">Remove</th>
                            </tr>
                        </thead>
                        <tbody id="recipeIngredientsTableBody">
                            </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnSaveDish">Save Recipe Dish</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="js/dishManagement.js"></script>
</body>
</html>