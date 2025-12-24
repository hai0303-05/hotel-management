<?php
session_start();                     // Khởi tạo session
require_once "../config/db.php";     // Kết nối CSDL

$id = $_GET['id'];                  // Lấy id phòng cần xóa

// Thực hiện xóa phòng
$conn->query("DELETE FROM rooms WHERE id = $id");

// Quay lại trang danh sách
header("Location: list.php");
