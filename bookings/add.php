<?php
session_start();

if (!isset($_SESSION['bookings'])) {
    $_SESSION['bookings'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking = [
        'customer_name' => $_POST['customer_name'],
        'room_number'   => $_POST['room_number'],
        'check_in'      => $_POST['check_in'],
        'check_out'     => $_POST['check_out']
    ];

    $_SESSION['bookings'][] = $booking;

    header("Location: list.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt phòng</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">
    <h1 class="page-title">ĐẶT PHÒNG</h1>

    <form method="post">
        <div class="form-group">
            <label>Tên khách hàng</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Phòng</label>
            <select name="room_number" class="form-control" required>
                <option value="">-- Chọn phòng --</option>
                <option value="101">101</option>
                <option value="102">102</option>
                <option value="103">103</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ngày nhận phòng</label>
            <input type="date" name="check_in" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Ngày trả phòng</label>
            <input type="date" name="check_out" class="form-control" required>
        </div>

        <button class="btn btn-primary">Đặt phòng</button>
    </form>
</div>

</body>
</html>
