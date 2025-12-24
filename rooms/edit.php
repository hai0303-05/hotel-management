<?php
session_start();                         // Bắt đầu session
require_once "../config/db.php";         // Kết nối database

$id = $_GET['id'];                      // Lấy id phòng từ URL

// Lấy thông tin phòng hiện tại
$result = $conn->query("SELECT * FROM rooms WHERE id = $id");
$room = $result->fetch_assoc();

// Khi người dùng submit form sửa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Lấy dữ liệu mới từ form
    $room_number = $_POST['room_number'];
    $room_type   = $_POST['room_type'];
    $price       = $_POST['price'];
    $status      = $_POST['status'];     // available | booked

    // Cập nhật thông tin phòng
    $sql = "UPDATE rooms SET
                room_number = '$room_number',
                room_type   = '$room_type',
                price       = '$price',
                status      = '$status'
            WHERE id = $id";

    $conn->query($sql);                  // Thực thi cập nhật

    header("Location: list.php");        // Quay về danh sách
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Room</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
    <h2 class="page-title">Edit Room</h2>

    <!-- Form sửa phòng -->
    <form method="post">

        <div class="form-group">
            <label>Room Number</label>
            <input class="form-control" name="room_number"
                   value="<?= $room['room_number'] ?>">
        </div>

        <div class="form-group">
            <label>Room Type</label>
            <input class="form-control" name="room_type"
                   value="<?= $room['room_type'] ?>">
        </div>

        <div class="form-group">
            <label>Price</label>
            <input class="form-control" name="price"
                   value="<?= $room['price'] ?>">
        </div>

        <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status">
                <option value="available"
                    <?= $room['status'] == 'available' ? 'selected' : '' ?>>
                    Available
                </option>
                <option value="booked"
                    <?= $room['status'] == 'booked' ? 'selected' : '' ?>>
                    Booked
                </option>
            </select>
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
</body>
</html>
