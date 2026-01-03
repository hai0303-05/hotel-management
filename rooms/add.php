<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once "config/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = trim($_POST['room_number']);
    $room_type   = trim($_POST['room_type']);
    $price       = trim($_POST['price']);

    if ($room_number === "" || $room_type === "" || $price === "") {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $check = $conn->query("SELECT id FROM rooms WHERE room_number = '$room_number'");
        if ($check->num_rows > 0) {
            $error = "Số phòng đã tồn tại.";
        } else {
            $conn->query("
                INSERT INTO rooms (room_number, room_type, price, status)
                VALUES ('$room_number', '$room_type', '$price', 'available')
            ");
            header("Location: index.php?page=rooms");
            exit;
        }
    }
}
?>
 
<div class="rooms-container">
    <div class="room-form">
        <h2>Thêm phòng</h2>

        <?php if ($error): ?>
            <div class="rooms-empty"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Số phòng</label>
                <input type="text" name="room_number" placeholder="Ví dụ: 101" required>
            </div>

            <div class="form-group">
                <label>Loại phòng</label>
                <input type="text" name="room_type" placeholder="Standard / Deluxe / VIP" required>
            </div>

            <div class="form-group">
                <label>Giá</label>
                <input type="number" name="price" placeholder="Ví dụ: 500000" required>
            </div>

            <div class="form-actions">
                <button class="rooms-btn rooms-btn-primary">Lưu</button>
                <a href="index.php?page=rooms"
                   class="rooms-btn rooms-btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
