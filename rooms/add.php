<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once "../config/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $room_number = trim($_POST['room_number']);
    $room_type   = trim($_POST['room_type']);
    $price       = trim($_POST['price']);

    if ($room_number === "" || $room_type === "" || $price === "") {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $check = $conn->query(
            "SELECT id FROM rooms WHERE room_number = '$room_number'"
        );

        if ($check->num_rows > 0) {
            $error = "Số phòng đã tồn tại.";
        } else {
            $conn->query(
                "INSERT INTO rooms (room_number, room_type, price, status)
                 VALUES ('$room_number', '$room_type', '$price', 'available')"
            );

            header("Location: index.php?page=rooms");
            exit;
        }
    }
}
?>

<div class="container">
    <h2 class="page-title">Thêm phòng</h2>

    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Số phòng</label>
            <input class="form-control" name="room_number" required>
        </div>

        <div class="form-group">
            <label>Loại phòng</label>
            <input class="form-control" name="room_type" required>
        </div>

        <div class="form-group">
            <label>Giá</label>
            <input class="form-control" type="number" name="price" required>
        </div>

        <button class="btn btn-primary">Lưu</button>
        <a href="index.php?page=rooms" class="btn">Quay lại</a>
    </form>
</div>
