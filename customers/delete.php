<?php
require_once "../config/db.php";

if (!isset($_GET['id'])) {
    die("Thieu id");
}

$id = (int)$_GET['id'];

// Neu khach dang luu tru -> KHONG cho xoa
$check = $conn->query("
    SELECT 1 FROM bookings
    WHERE customer_id = $id
      AND check_in IS NOT NULL
      AND check_out IS NULL
");

if ($check && $check->num_rows > 0) {
    die("Khach dang luu tru, khong the xoa");
}

// Con lai: dat phong chua check-in HOAC da tung luu tru -> XOA DUOC
$conn->query("DELETE FROM customers WHERE id = $id");

header("Location: list.php");
exit;
