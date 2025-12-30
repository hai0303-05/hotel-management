<?php
session_start();

/* ===== CHAN CHUA DANG NHAP ===== */
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hotel Management</title>

    <!-- CSS chung (sau này chỉ việc viết file này) -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">

    <h1 class="page-title">Hotel Management System</h1>

    <p>
        Xin chào: <b><?php echo $_SESSION['username']; ?></b>
        (<?php echo $_SESSION['role']; ?>)
    </p>

    <hr>

    <!-- ===== MENU CHINH ===== -->
    <ul>
        <!-- DUNG CHO CA ADMIN & STAFF -->
        <li>
            <a href="rooms/list.php">Quản lý phòng</a>
        </li>

        <li>
            <a href="customers/list.php">Khách hàng</a>
        </li>

        <li>
            <a href="bookings/list.php">Đặt phòng</a>
        </li>

        <!-- ===== CHI ADMIN ===== -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li>
                <a href="admin/stats.php">Thống kê doanh thu</a>
            </li>
            <li>
                <a href="admin/users.php">Quản lý tài khoản</a>
            </li>
        <?php endif; ?>

        <li>
            <a href="auth/logout.php">Đăng xuất</a>
        </li>
    </ul>

</div>

</body>
</html>
