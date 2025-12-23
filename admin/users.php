<?php
require_once '../auth/check_login.php';
checkRole('admin');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quan ly nguoi dung</title>
</head>
<body>

<h2>Quan ly nguoi dung</h2>

<p>Xin chao Admin: <?php echo $_SESSION['username']; ?></p>

<p>Trang nay chi admin moi duoc truy cap.</p>

<p>
    <a href="stats.php">Thong ke</a> |
    <a href="../index.php">Trang chu</a> |
    <a href="../auth/logout.php">Dang xuat</a>
</p>

</body>
</html>
