<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
include "../database/connection.php";

// Stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM product"))['c'];
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM user WHERE user_role='client'"))['c'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM payment"))['c'];
$revenue        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) AS r FROM payment WHERE status='paid'"))['r'] ?? 0;
$total_cats     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM category"))['c'];

// All products with category name
$products = mysqli_query($conn, "SELECT p.*, c.name AS cat_name FROM product p LEFT JOIN category c ON p.category_id = c.category_id ORDER BY p.created_at DESC");

// Recent payments
$recent_orders = mysqli_query($conn, "SELECT py.*, u.user_name FROM payment py JOIN user u ON py.user_id = u.user_id ORDER BY py.created_at DESC LIMIT 5");

// All categories
$categories      = mysqli_query($conn, "SELECT * FROM category ORDER BY name ASC");
$categories_arr  = [];
while ($cat = mysqli_fetch_assoc($categories)) {
    $categories_arr[] = $cat;
}

$name    = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
$init    = strtoupper(substr($name, 0, 1));
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$tab     = $_GET['tab'] ?? 'products';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — ShopX</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="dashboard-body">
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">Shop<span style="color:var(--primary-color)">X</span></div>
        <nav class="sidebar-nav">
            <a href="dashboard.php?tab=products" class="nav-item <?= $tab === 'products' ? 'active' : '' ?>"><i class="fa-solid fa-box"></i> Products</a>
            <a href="dashboard.php?tab=categories" class="nav-item <?= $tab === 'categories' ? 'active' : '' ?>"><i class="fa-solid fa-tags"></i> Categories</a>
            <a href="index.php" class="nav-item"><i class="fa-solid fa-house"></i> Storefront</a>
            <a href="../controller/logout.php" class="nav-item danger"><i class="fa-solid fa-door-open"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="top-bar">
            <div style="display:flex; align-items:center;">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <input type="text" class="search-bar" placeholder="Search..." id="searchInput" oninput="filterTable()">
            </div>
            <div class="user-profile">
                <span class="user-role-badge admin">Admin</span>
                <span class="user-email"><?= $name ?></span>
                <div class="avatar"><?= $init ?></div>
            </div>
        </header>

        <section class="dashboard-content">
            <?php if ($success): ?>
                <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?= $success ?></div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <div class="stat-info">
                        <div class="value"><?= $total_products ?></div>
                        <div class="label">Total Products</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info">
                        <div class="value"><?= $total_users ?></div>
                        <div class="label">Customers</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fa-solid fa-receipt"></i></div>
                    <div class="stat-info">
                        <div class="value"><?= $total_orders ?></div>
                        <div class="label">Orders</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <div class="value" style="font-size:1.5rem"><?= number_format($revenue) ?> FCFA</div>
                        <div class="label revenue">Total Revenue</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fa-solid fa-tags"></i></div>
                    <div class="stat-info">
                        <div class="value"><?= $total_cats ?></div>
                        <div class="label">Categories</div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="table-card" style="margin-top: 2rem;">
                <div class="table-header">
                    <h3>Recent Orders</h3>
                    <a href="#" class="btn btn-outline btn-sm">View All Orders</a>
                </div>
                <div class="order-list">
                    <?php if (mysqli_num_rows($recent_orders) === 0): ?>
                        <div style="text-align:center; padding: 2rem; color: var(--text-muted);">No orders yet.</div>
                    <?php else: ?>
                        <?php while ($ord = mysqli_fetch_assoc($recent_orders)): ?>
                            <div class="order-row">
                                <div class="order-info-main">
                                    <div class="stat-icon purple" style="width:40px;height:40px;font-size:1.1rem;"><i class="fa-solid fa-receipt"></i></div>
                                    <div>
                                        <div style="font-weight:700; color:var(--text-main);">Order #<?= $ord['payment_id'] ?></div>
                                        <div style="font-size:0.8rem; color:var(--text-muted);"><?= htmlspecialchars($ord['user_name']) ?> • <?= date('M d, Y', strtotime($ord['created_at'])) ?></div>
                                    </div>
                                </div>
                                <div class="order-details-right">
                                    <div style="font-weight:800; color:var(--primary-color);"><?= number_format($ord['total_price']) ?> FCFA</div>
                                    <span class="status-pill <?= $ord['status'] === 'paid' ? 'success' : 'pending' ?>">
                                        <?= htmlspecialchars($ord['status']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ===== PRODUCTS TAB ===== -->
            <?php if ($tab === 'products'): ?>
            <div class="table-card">
                <div class="table-header">
                    <h3>All Products</h3>
                    <button class="btn btn-primary btn-sm" onclick="openModal('addModal')"><i class="fa-solid fa-plus"></i> Add Product</button>
                </div>
                <table id="productTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($products) === 0): ?>
                        <tr><td colspan="6" style="text-align:center; color: var(--text-muted); padding: 2rem;">No products yet. Add your first product!</td></tr>
                    <?php else: ?>
                    <?php while ($p = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.8rem;"><?= $p['product_id'] ?></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <?php if (!empty($p['image'])): ?>
                                        <img src="../assets/image/<?= htmlspecialchars($p['image']) ?>" style="width:40px;height:40px;border-radius:8px;object-fit:cover;">
                                    <?php else: ?>
                                        <div style="width:40px;height:40px;border-radius:8px;background:var(--surface);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--text-muted);"><i class="fa-solid fa-image"></i></div>
                                    <?php endif; ?>
                                    <span style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($p['name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if ($p['cat_name']): ?>
                                    <span class="status-pill pending" style="font-size:0.72rem;"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($p['cat_name']) ?></span>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);font-size:0.8rem;">— Uncategorised</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight:700;color:var(--primary-color);"><?= number_format($p['price']) ?> FCFA</td>
                            <td>
                                <span class="status-pill <?= $p['quantity'] > 5 ? 'success' : ($p['quantity'] > 0 ? 'pending' : 'danger') ?>">
                                    <?= $p['quantity'] == 0 ? 'Out of stock' : ($p['quantity'] < 5 ? '<i class="fa-solid fa-triangle-exclamation"></i> Low Stock: ' . $p['quantity'] : $p['quantity'] . ' in stock') ?>
                                </span>
                            </td>
                            <td>
                                <div class="admin-actions">
                                    <button class="btn btn-outline btn-sm" onclick="openEditModal(
                                        <?= $p['product_id'] ?>,
                                        '<?= addslashes(htmlspecialchars($p['name'])) ?>',
                                        '<?= addslashes(htmlspecialchars($p['description'])) ?>',
                                        '<?= $p['price'] ?>',
                                        '<?= $p['quantity'] ?>',
                                        '<?= $p['category_id'] ?? '' ?>'
                                    )"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                    <form method="POST" action="../controller/productcontroller.php" onsubmit="return confirm('Delete this product?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ===== CATEGORIES TAB ===== -->
            <?php elseif ($tab === 'categories'): ?>
            <div class="table-card">
                <div class="table-header">
                    <h3>All Categories</h3>
                    <button class="btn btn-primary btn-sm" onclick="openModal('addCatModal')"><i class="fa-solid fa-plus"></i> Add Category</button>
                </div>
                <table id="catTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($categories_arr)): ?>
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem;">No categories yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($categories_arr as $cat): ?>
                        <tr>
                            <td style="color:var(--text-muted);font-size:0.8rem;"><?= $cat['category_id'] ?></td>
                            <td style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($cat['name']) ?></td>
                            <td><code style="background:var(--surface);padding:0.2rem 0.5rem;border-radius:4px;font-size:0.8rem;"><?= htmlspecialchars($cat['slug']) ?></code></td>
                            <td style="color:var(--text-muted);max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($cat['description'] ?? '') ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="btn btn-outline btn-sm" onclick="openEditCatModal(<?= $cat['category_id'] ?>, '<?= addslashes(htmlspecialchars($cat['name'])) ?>', '<?= addslashes(htmlspecialchars($cat['description'] ?? '')) ?>')">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <form method="POST" action="../controller/categorycontroller.php" onsubmit="return confirm('Delete this category? Products in it will become uncategorised.')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </section>
    </main>
    <div class="nav-overlay" id="dashboardOverlay"></div>
</div>

<!-- Add Product Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add New Product</h3>
            <button class="modal-close" onclick="closeModal('addModal')">✕</button>
        </div>
        <form method="POST" action="../controller/productcontroller.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="data">
                <div class="input-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" placeholder="e.g. Wireless Headphones" required>
                </div>
                <div class="input-group">
                    <label>Category</label>
                    <select name="category_id" style="padding:0.7rem 1rem;background:var(--surface);border:1px solid var(--border-color);border-radius:8px;color:var(--text-main);font-size:0.9rem;font-family:Inter,sans-serif;width:100%;">
                        <option value="">— No Category —</option>
                        <?php foreach ($categories_arr as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe your product..."></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="input-group">
                        <label>Price (FCFA) *</label>
                        <input type="number" name="price" step="0.01" min="0" placeholder="10,000" required>
                    </div>
                    <div class="input-group">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" min="0" placeholder="100" required>
                    </div>
                </div>
                <div class="input-group">
                    <label>Product Image</label>
                    <input type="file" name="image" accept="image/*" style="padding:0.5rem;background:var(--surface);border:1px solid var(--border-color);border-radius:8px;color:var(--text-muted);">
                </div>
                <button type="submit">Add Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Product</h3>
            <button class="modal-close" onclick="closeModal('editModal')">✕</button>
        </div>
        <form method="POST" action="../controller/productcontroller.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="product_id" id="editId">
            <div class="data">
                <div class="input-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                <div class="input-group">
                    <label>Category</label>
                    <select name="category_id" id="editCategory" style="padding:0.7rem 1rem;background:var(--surface);border:1px solid var(--border-color);border-radius:8px;color:var(--text-main);font-size:0.9rem;font-family:Inter,sans-serif;width:100%;">
                        <option value="">— No Category —</option>
                        <?php foreach ($categories_arr as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" id="editDesc"></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="input-group">
                        <label>Price (FCFA) *</label>
                        <input type="number" name="price" id="editPrice" step="0.01" min="0" required>
                    </div>
                    <div class="input-group">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" id="editQty" min="0" required>
                    </div>
                </div>
                <div class="input-group">
                    <label>New Image (leave blank to keep current)</label>
                    <input type="file" name="image" accept="image/*" style="padding:0.5rem;background:var(--surface);border:1px solid var(--border-color);border-radius:8px;color:var(--text-muted);">
                </div>
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal-overlay" id="addCatModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add Category</h3>
            <button class="modal-close" onclick="closeModal('addCatModal')">✕</button>
        </div>
        <form method="POST" action="../controller/categorycontroller.php">
            <input type="hidden" name="action" value="add">
            <div class="data">
                <div class="input-group">
                    <label>Category Name *</label>
                    <input type="text" name="name" placeholder="e.g. Electronics" required>
                </div>
                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Brief description..."></textarea>
                </div>
                <button type="submit">Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal-overlay" id="editCatModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Category</h3>
            <button class="modal-close" onclick="closeModal('editCatModal')">✕</button>
        </div>
        <form method="POST" action="../controller/categorycontroller.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="category_id" id="editCatId">
            <div class="data">
                <div class="input-group">
                    <label>Category Name *</label>
                    <input type="text" name="name" id="editCatName" required>
                </div>
                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" id="editCatDesc"></textarea>
                </div>
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function openEditModal(id, name, desc, price, qty, catId) {
    document.getElementById('editId').value       = id;
    document.getElementById('editName').value     = name;
    document.getElementById('editDesc').value     = desc;
    document.getElementById('editPrice').value    = price;
    document.getElementById('editQty').value      = qty;
    document.getElementById('editCategory').value = catId || '';
    openModal('editModal');
}

function openEditCatModal(id, name, desc) {
    document.getElementById('editCatId').value   = id;
    document.getElementById('editCatName').value = name;
    document.getElementById('editCatDesc').value = desc;
    openModal('editCatModal');
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('active'); });
});

// Sidebar Toggle
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.querySelector('.sidebar');
const dashboardOverlay = document.getElementById('dashboardOverlay');

function toggleSidebar() {
    sidebar.classList.toggle('active');
    dashboardOverlay.classList.toggle('active');
    sidebarToggle.innerHTML = sidebar.classList.contains('active') 
        ? '<i class="fa-solid fa-xmark"></i>' 
        : '<i class="fa-solid fa-bars"></i>';
    
    if (window.innerWidth <= 992) {
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }
}

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleSidebar);
}

if (dashboardOverlay) {
    dashboardOverlay.addEventListener('click', toggleSidebar);
}

// Close sidebar on item click (mobile)
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', () => {
        if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
            toggleSidebar();
        }
    });
});

// Live search filter
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    ['productTable', 'catTable'].forEach(tid => {
        const tbl = document.getElementById(tid);
        if (!tbl) return;
        tbl.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
</script>
</body>
</html>