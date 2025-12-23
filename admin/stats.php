<?php
require_once '../auth/check_login.php';
checkRole('admin');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thong ke</title>
</head>
<body>

<h2>Thong ke he thong</h2>

<p>Chi admin moi duoc xem thong ke.</p>

<a href="admin.php">Quay lai trang quan tri</a>

</body>
</html>
