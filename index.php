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
    <title>BiteBalance - Dashboard</title>
    <style>
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
        .calendar-header-day { text-align: center; font-weight: bold; font-size: 0.85rem; color: #198754; padding-bottom: 5px; }
        .calendar-day-cell { min-height: 50px; display: flex; flex-direction: column; justify-content: space-between; padding: 5px; cursor: pointer; border-radius: 8px; transition: all 0.2s; border: 1px solid #e9ecef; background: #fff; }
        .calendar-day-cell:hover { background-color: #f1f3f5; border-color: #ced4da; }
        .calendar-day-cell.other-month { opacity: 0.4; }
        .calendar-day-cell.active-selected { border-color: #198754; box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.25); font-weight: bold; }
        .calendar-day-cell.real-today { background-color: rgba(25, 135, 84, 0.1) !important; border-color: #198754 !important; }
    </style>
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

<nav>
    <div class="container-fluid bg-white py-2 shadow-sm border-bottom">
        <div class="d-flex align-items-center justify-content-center flex-wrap gap-2">
            <ul id="weekCalNav" class="nav nav-underline gap-2 gap-md-4 fs-5 justify-content-center mb-0">
            </ul>
            <button type="button" id="btnOpenMonthView" class="btn btn-outline-success btn-sm ms-md-3 px-3 rounded-pill">
                <i class="bi bi-calendar3 me-1"></i> Month View
            </button>
        </div>
    </div>  
</nav>

<main class="container py-4" style="max-width: 900px;">
    
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 bg-white">
        <h4 class="fw-bold text-dark mb-3">Daily Energy Status (<span id="txtSummaryDate">Today</span>)</h4>
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3 border-end">
                <div class="fs-2 fw-bold text-success" id="totCalories">0</div>
                <div class="text-muted small fw-bold text-uppercase">Calories (kcal)</div>
            </div>
            <div class="col-6 col-md-3 border-end-md">
                <div class="fs-3 fw-bold text-primary" id="totProtein">0g</div>
                <div class="text-muted small fw-bold text-uppercase">Protein</div>
            </div>
            <div class="col-6 col-md-3 border-end">
                <div class="fs-3 fw-bold text-danger" id="totFat">0g</div>
                <div class="text-muted small fw-bold text-uppercase">Fat</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-3 fw-bold text-warning-emphasis" id="totCarbs">0g</div>
                <div class="text-muted small fw-bold text-uppercase">Carbohydrates</div>
            </div>
        </div>
    </div>

    <section class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="px-4 py-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-between">
            <h3 class="text-success fw-bold mb-0 fs-4"><i class="bi bi-brightness-alt-high me-2"></i>Breakfast</h3>
            <button type="button" class="btn btn-success btn-sm rounded-pill px-3" onclick="openAddEntryModal('breakfast')">
                <i class="bi bi-plus-lg me-1"></i>Log Item
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-muted small">
                <tbody id="mealTableBody-breakfast">
                </tbody>
            </table>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="px-4 py-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-between">
            <h3 class="text-success fw-bold mb-0 fs-4"><i class="bi bi-brightness-high me-2"></i>Lunch</h3>
            <button type="button" class="btn btn-success btn-sm rounded-pill px-3" onclick="openAddEntryModal('lunch')">
                <i class="bi bi-plus-lg me-1"></i>Log Item
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-muted small">
                <tbody id="mealTableBody-lunch">
                </tbody>
            </table>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="px-4 py-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-between">
            <h3 class="text-success fw-bold mb-0 fs-4"><i class="bi bi-moon-stars me-2"></i>Dinner</h3>
            <button type="button" class="btn btn-success btn-sm rounded-pill px-3" onclick="openAddEntryModal('dinner')">
                <i class="bi bi-plus-lg me-1"></i>Log Item
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-muted small">
                <tbody id="mealTableBody-dinner">
                </tbody>
            </table>
        </div>
    </section>
</main>

<div class="modal fade" id="modalMonthView" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-success text-white">
                <div class="d-flex align-items-center justify-content-between w-100 me-2">
                    <button type="button" class="btn btn-link text-white p-0" id="btnPrevMonth"><i class="bi bi-chevron-left fs-4"></i></button>
                    <h5 class="modal-title fw-bold mb-0" id="txtMonthViewTitle">Select Day</h5>
                    <button type="button" class="btn btn-link text-white p-0" id="btnNextMonth"><i class="bi bi-chevron-right fs-4"></i></button>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="calendar-grid mb-1">
                    <div class="calendar-header-day">M</div>
                    <div class="calendar-header-day">T</div>
                    <div class="calendar-header-day">W</div>
                    <div class="calendar-header-day">T</div>
                    <div class="calendar-header-day">F</div>
                    <div class="calendar-header-day">S</div>
                    <div class="calendar-header-day">S</div>
                </div>
                <div id="monthGridCellsContainer" class="calendar-grid"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAddEntry" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="entryModalTitle">Log Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="inputMealType">
                <input type="hidden" id="selectEntryItem" value="">
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">What are you logging? *</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="entryType" id="typeProduct" value="product" checked>
                        <label class="btn btn-outline-success" for="typeProduct"><i class="bi bi-egg-fried me-1"></i>Single Product</label>
                        
                        <input type="radio" class="btn-check" name="entryType" id="typeDish" value="dish">
                        <label class="btn btn-outline-success" for="typeDish"><i class="bi bi-journal-text me-1"></i>Custom Dish Recipe</label>
                    </div>
                </div>

                <div class="mb-3 position-relative">
                    <label class="form-label fw-bold small text-muted" id="lblSelectTarget">Search Product Item *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="inputItemSearch" class="form-control border-start-0 ps-0" placeholder="Type to search..." autocomplete="off">
                    </div>
                    
                    <div id="searchAutocompleteWrapper" class="list-group shadow border rounded-3 position-absolute w-100 mt-1 d-none" style="z-index: 1055; max-height: 200px; overflow-y: auto;">
                    </div>
                    
                    <div id="divSelectionFeedback" class="mt-2 small d-none">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle p-2 w-100 text-start text-truncate">
                            <i class="bi bi-check-circle-fill me-1"></i> Selected: <span id="txtSelectedLabel" class="fw-bold">None</span>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Portion Size Eaten *</label>
                    <div class="input-group">
                        <input type="number" id="entryWeight" class="form-control" min="1" max="5000" value="100" required>
                        <span class="input-group-text bg-light text-muted">g</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success px-4 fw-bold" id="btnSaveEntry">Log to Dashboard</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="js/dashboardManagement.js"></script>
</body>
</html>