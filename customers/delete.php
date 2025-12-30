<?php
require_once __DIR__ . '/../config/db.php';

$id = (int)$_GET['id'];

// Kiểm tra xem khách có đang thuê phòng không
$check = $conn->query("SELECT * FROM bookings WHERE customer_id=$id AND check_in IS NOT NULL AND check_out IS NULL");

if ($check->num_rows > 0) {
    die('Khách đang đặt phòng, không thể xóa');
}

$conn->query("DELETE FROM customers WHERE id=$id");
header('Location: list.php');