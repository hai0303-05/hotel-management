<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once "config/db.php";

$id = $_GET['id'] ?? 0;
$room = $conn->query("SELECT * FROM rooms WHERE id = $id")->fetch_assoc();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $room_number = trim($_POST['room_number']);
    $room_type   = trim($_POST['room_type']);
    $price       = trim($_POST['price']);

    if ($room_number === "" || $room_type === "" || $price === "") {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $check = $conn->query(
            "SELECT id FROM rooms 
             WHERE room_number = '$room_number' AND id != $id"
        );

        if ($check->num_rows > 0) {
            $error = "Số phòng đã tồn tại.";
        } else {
            $conn->query(
                "UPDATE rooms SET
                    room_number = '$room_number',
                    room_type   = '$room_type',
                    price       = '$price'
                 WHERE id = $id"
            );

            header("Location: index.php?page=rooms");
            exit;
        }
    }
}
?>

<div class="container">
    <h2 class="page-title">Sửa phòng</h2>

    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Số phòng</label>
            <input class="form-control" name="room_number"
                   value="<?= $room['room_number'] ?>" required>
        </div>

        <div class="form-group">
            <label>Loại phòng</label>
            <input class="form-control" name="room_type"
                   value="<?= $room['room_type'] ?>" required>
        </div>

        <div class="form-group">
            <label>Giá</label>
            <input class="form-control" type="number" name="price"
                   value="<?= $room['price'] ?>" required>
        </div>

        <button class="btn btn-primary">Cập nhật</button>
        <a href="index.php?page=rooms" class="btn">Quay lại</a>
    </form>
</div>