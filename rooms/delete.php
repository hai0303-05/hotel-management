<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once "config/db.php";

/* 1️⃣ ÉP KIỂU ID */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'ID phòng không hợp lệ';
    header("Location: index.php?page=rooms");
    exit;
}

/* 2️⃣ CHECK BẢNG BOOKINGS (KHÔNG CHECK STATUS) */
$check = $conn->query("
    SELECT 1
    FROM bookings
    WHERE room_id = $id
    LIMIT 1
");

if ($check && $check->num_rows > 0) {
    $_SESSION['error'] = 'Phòng đã có lịch sử đặt, không thể xóa';
    header("Location: index.php?page=rooms");
    exit;
}

/* 3️⃣ DELETE */
$conn->query("DELETE FROM rooms WHERE id = $id");

/* 4️⃣ REDIRECT + EXIT (BẮT BUỘC) */
header("Location: index.php?page=rooms");
exit;
