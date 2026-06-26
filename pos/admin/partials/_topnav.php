<?php
$admin_id = $_SESSION['admin_id'];
//$login_id = $_SESSION['login_id'];
$ret = "SELECT * FROM  rpos_admin  WHERE admin_id = '$admin_id'";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
$adminPageFile = basename($_SERVER['PHP_SELF'], '.php');
$adminPageTitles = [
    'dashboard' => 'Dashboard',
    'products' => 'Products',
    'categories' => 'Categories',
    'add_product' => 'Add Product',
    'update_product' => 'Edit Product',
    'add_catg' => 'Add Category',
    'update_catg' => 'Edit Category',
    'hrm' => 'Employees',
    'add_staff' => 'Add Employee',
    'update_staff' => 'Edit Employee',
    'orders' => 'Orders',
    'orders_reports' => 'Orders Reports',
    'payments' => 'Payments',
    'payments_reports' => 'Payments Reports',
    'receipts' => 'Receipts',
    'change_profile' => 'Profile',
    'settings' => 'Settings',
    'invo' => 'Invoice',
    'newi' => 'Invoice',
    'print_receipt' => 'Receipt',
];
$adminPageTitle = $adminPageTitles[$adminPageFile] ?? ucwords(str_replace('_', ' ', $adminPageFile));
while ($admin = $res->fetch_object()) {

?>

    <nav class="navbar navbar-top navbar-expand-md navbar-light admin-navbar" id="navbar-main">
        <div class="container-fluid">
            <div class="admin-navbar-title">
                <a class="admin-navbar-heading" href="<?php echo htmlspecialchars($adminPageFile); ?>.php"><?php echo htmlspecialchars($adminPageTitle); ?></a>
                <span class="admin-navbar-subtitle"><?php echo htmlspecialchars($admin->admin_name); ?></span>
            </div>

            <form class="admin-navbar-search d-none d-md-flex" role="search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input class="form-control" type="search" placeholder="Search products, orders, reports" aria-label="Search">
            </form>

            <div class="admin-navbar-actions ml-auto">
                <a class="admin-icon-button" href="invo.php" title="Orders" aria-label="Orders">
                    <i class="bi bi-cart3" aria-hidden="true"></i>
                </a>
                <a class="admin-icon-button" href="dashboard.php" title="Dashboard" aria-label="Dashboard">
                    <i class="bi bi-speedometer2" aria-hidden="true"></i>
                </a>
                <a class="admin-icon-button" href="payments_reports.php" title="Reports" aria-label="Reports">
                    <span class="notification-dot"></span>
                    <i class="bi bi-bell" aria-hidden="true"></i>
                </a>
            </div>

            <ul class="navbar-nav align-items-center d-none d-md-flex">
                <li class="nav-item dropdown">
                    <a class="nav-link pr-0 admin-profile-button" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media align-items-center">
                            <span class="avatar avatar-sm">
                                <img alt="Image placeholder" src="assets/img/theme/user-a-min.png">
                            </span>
                            <div class="media-body ml-2 d-none d-lg-block">
                                <span class="mb-0 text-sm font-weight-bold"><?php echo htmlspecialchars($admin->admin_name); ?></span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                        <div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome!</h6>
                        </div>
                        <a href="change_profile.php" class="dropdown-item">
                            <i class="ni ni-single-02"></i>
                            <span>My profile</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <i class="ni ni-user-run"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
<?php } ?>
