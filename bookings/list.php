<?php
session_start();

$bookings = $_SESSION['bookings'] ?? [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách đặt phòng</title>
    <link rel="stylesheet" href="../assets/style.css">
    <?php if (isset($_GET['checkout'])): ?>
    <p style="color: green;">Trả phòng thành công?</p>
<?php endif; ?>

</head>
<body>

<div class="container">
    <h1 class="page-title">Danh sách khách đang thuê</h1>

    <?php if (isset($_GET['success'])): ?>
        <p style="color: green;">Đặt phòng thành công!</p>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Khách hàng</th>
                <th>Phòng</th>
                <th>Ngày nhận</th>
                <th>Ngày trả</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>

        <?php if (empty($bookings)): ?>
            <tr>
                <td colspan="5">Chưa có khách đặt phòng</td>
            </tr>
        <?php else: ?>
            <?php foreach ($bookings as $index => $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['customer_name']) ?></td>
                    <td><?= $b['room_number'] ?></td>
                    <td><?= $b['check_in'] ?></td>
                    <td><?= $b['check_out'] ?></td>
                    <td>
                        <a href="checkout.php?index=<?= $index ?>" class="btn btn-primary">
                            Trả phòng
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
    </table>
</div>

</body>
</html>
