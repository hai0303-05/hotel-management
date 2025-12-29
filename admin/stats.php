<?php
require_once '../auth/check_login.php';
checkRole('admin'); // chi admin moi vao duoc

require_once '../config/db.php';

/* ===== THONG KE ===== */
$total_users = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users")
)['total'];

$total_admin = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='admin'")
)['total'];

$total_staff = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='staff'")
)['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thong ke he thong</title>
</head>
<body>

<h2>Thong ke he thong</h2>

<p>Xin chao Admin: <b><?php echo $_SESSION['username']; ?></b></p>

<ul>
    <li>Tong so tai khoan: <b><?php echo $total_users; ?></b></li>
    <li>So Admin: <b><?php echo $total_admin; ?></b></li>
    <li>So Nhan vien: <b><?php echo $total_staff; ?></b></li>
</ul>

<p>
    <a href="users.php">Quan ly tai khoan</a> |
    <a href="../index.php">Trang chu</a> |
    <a href="../auth/logout.php">Dang xuat</a>
</p>

</body>
</html>
