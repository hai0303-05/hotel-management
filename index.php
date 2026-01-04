<?php
session_start();

/* ===== CHECK LOGIN ===== */
require_once 'auth/check_login.php';

/* ===== CHẶN TRUY CẬP TRỰC TIẾP FILE CON ===== */
define('IN_INDEX', true);

/* ===== PAGE ===== */
$page = $_GET['page'] ?? '';

/* ===== ROUTES ===== */
$routes = [
    /* ===== ROOMS ===== */
    'rooms'          => 'rooms/list.php',
    'rooms_add'      => 'rooms/add.php',
    'rooms_edit'     => 'rooms/edit.php',
    'rooms_delete'   => 'rooms/delete.php', // ✅ FIX: thêm route delete

    /* ===== BOOKINGS ===== */
    'bookings'         => 'bookings/list.php',
    'bookings_add'     => 'bookings/add.php',
    'bookings_edit'    => 'bookings/edit.php',
    'bookings_checkout'=> 'bookings/checkout.php',

    /* ===== CUSTOMERS ===== */
    'customers'      => 'customers/list.php',
    'customers_add'  => 'customers/add.php',
    'customers_edit' => 'customers/edit.php',

    /* ===== ADMIN ===== */
    'admin_users'    => 'admin/users.php',
    'admin_stats'    => 'admin/stats.php',
];

/* ===== CSS THEO MODULE ===== */
$moduleCss = '';
if (str_starts_with($page, 'rooms'))      $moduleCss = 'rooms.css';
elseif (str_starts_with($page, 'bookings'))   $moduleCss = 'bookings.css';
elseif (str_starts_with($page, 'customers'))  $moduleCss = 'customers.css';
elseif (str_starts_with($page, 'admin'))      $moduleCss = 'admin.css';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hotel Management</title>

    <!-- CSS CHUNG -->
    <link rel="stylesheet" href="assets/index.css">

    <!-- CSS MODULE -->
    <?php if ($moduleCss): ?>
        <link rel="stylesheet" href="assets/<?php echo $moduleCss; ?>">
    <?php endif; ?>
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="layout-header">
    <h1>Hotel Management</h1>
    <div class="header-user">
        Xin chào <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
        | <a href="auth/logout.php">Đăng xuất</a>
    </div>
</header>

<!-- ===== MENU ===== -->
<nav class="layout-menu">
    <a href="index.php">Trang chủ</a>
    <a href="index.php?page=rooms">Phòng</a>
    <a href="index.php?page=bookings">Đặt phòng</a>
    <a href="index.php?page=bookings_checkout">Trả phòng</a>
    <a href="index.php?page=customers">Khách hàng</a>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="index.php?page=admin_users">Tài khoản</a>
        <a href="index.php?page=admin_stats">Doanh thu</a>
    <?php endif; ?>
</nav>

<!-- ===== CONTENT ===== -->
<main class="layout-content">
<?php
if ($page !== '' && isset($routes[$page])) {
    include $routes[$page];
} else {
    /* ===== DASHBOARD ===== */
    ?>
    <div class="dashboard-banner">
        <div class="dashboard-icon">🏨</div>
        <h2>Hotel Management System</h2>
        <p>
            Chào mừng <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
            (<?php echo $_SESSION['role'] === 'admin' ? 'Quản lý' : 'Nhân viên'; ?>)
        </p>
        <p class="dashboard-note">
            Chọn chức năng ở menu phía trên để bắt đầu thao tác
        </p>
    </div>
    <?php
}
?>
</main>

<!-- ===== FOOTER ===== -->
<footer class="layout-footer">
    © <?php echo date('Y'); ?> Hotel Management System
</footer>

</body>
</html>
