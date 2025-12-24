<?php
session_start();                      // Khởi động session dùng chung cho toàn hệ thống
require_once "../config/db.php";      // Kết nối CSDL, chỉ dùng biến $conn

// Kiểm tra khi người dùng submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Lấy dữ liệu từ form
    $room_number = $_POST['room_number'];   // Số phòng
    $room_type   = $_POST['room_type'];     // Loại phòng
    $price       = $_POST['price'];         // Giá phòng

    // Thêm phòng mới, trạng thái mặc định là 'available'
    $sql = "INSERT INTO rooms (room_number, room_type, price, status)
            VALUES ('$room_number', '$room_type', '$price', 'available')";

    $conn->query($sql);               // Thực thi câu lệnh SQL

    header("Location: list.php");     // Quay về trang danh sách phòng
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Room</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
    <h2 class="page-title">Add Room</h2>

    <!-- Form thêm phòng -->
    <form method="post">

        <div class="form-group">
            <label>Room Number</label>
            <input class="form-control" name="room_number" required>
        </div>

        <div class="form-group">
            <label>Room Type</label>
            <input class="form-control" name="room_type" required>
        </div>

        <div class="form-group">
            <label>Price</label>
            <input class="form-control" type="number" name="price" required>
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
</body>
</html>
