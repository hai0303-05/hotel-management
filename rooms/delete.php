<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once "config/db.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?page=rooms");
    exit;
}

/* Check phòng đang có khách hay không */
$check = $conn->query("
    SELECT 1
    FROM bookings
    WHERE room_id = $id
      AND check_out IS NULL
    LIMIT 1
");

if ($check && $check->num_rows > 0) {
    $_SESSION['error'] = 'Phòng đang có khách, không thể xóa';
    header("Location: index.php?page=rooms");
    exit;
}

/* XÓA PHÒNG */
$conn->query("DELETE FROM rooms WHERE id = $id");

/* Quay lại danh sách */
header("Location: index.php?page=rooms");
exit;
