<?php
session_start();

$bookings = $_SESSION['bookings'] ?? [];

// khách được chọn
$selected_index = $_GET['index'] ?? null;
$selected_booking = null;

if ($selected_index !== null && isset($bookings[$selected_index])) {
    $selected_booking = $bookings[$selected_index];
}

// xử lý xác nhận trả phòng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_checkout'])) {
    $index = $_POST['index'];
    unset($_SESSION['bookings'][$index]);
    $_SESSION['bookings'] = array_values($_SESSION['bookings']); // reset index
    header("Location: list.php?checkout=success");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn thanh toán</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">
    <h1 class="page-title">HÓA ĐƠN THANH TOÁN</h1>

    <!-- Ô tìm khách -->
    <div class="form-group">
        <label>Khách đang thuê</label>
        <input type="text" class="form-control" placeholder="Gõ tên khách hàng...">
    </div>

    <hr>

    <!-- BẢNG DANH SÁCH KHÁCH -->
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
                <td colspan="5">Không có khách đang thuê</td>
            </tr>
        <?php else: ?>
            <?php foreach ($bookings as $i => $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['customer_name']) ?></td>
                    <td><?= $b['room_number'] ?></td>
                    <td><?= $b['check_in'] ?></td>
                    <td><?= $b['check_out'] ?></td>
                    <td>
                        <a href="checkout.php?index=<?= $i ?>" class="btn btn-primary">
                            Chọn
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- HÓA ĐƠN -->
    <?php if ($selected_booking): ?>
        <hr>

        <?php
        $days = (strtotime($selected_booking['check_out']) - strtotime($selected_booking['check_in'])) / 86400;
        $price_per_day = 500000;
        $total = $days * $price_per_day;
        ?>

        <p><strong>Khách hàng:</strong> <?= $selected_booking['customer_name'] ?></p>
        <p><strong>Phòng:</strong> <?= $selected_booking['room_number'] ?></p>
        <p><strong>Ngày nhận:</strong> <?= $selected_booking['check_in'] ?></p>
        <p><strong>Ngày trả:</strong> <?= $selected_booking['check_out'] ?></p>

        <p><strong>Số ngày thuê:</strong> <?= $days ?> ngày</p>
        <p><strong>Giá / ngày:</strong> <?= number_format($price_per_day) ?> VND</p>

        <h2>TỔNG TIỀN: <?= number_format($total) ?> VND</h2>

        <form method="post">
            <input type="hidden" name="index" value="<?= $selected_index ?>">
            <button type="submit" name="confirm_checkout" class="btn btn-danger">
                xác nhận trả phòng?
            </button>
        </form>
    <?php endif; ?>

</div>
</body>
</html>
