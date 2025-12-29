<?php
session_start();
require_once "../config/db.php";

$id = $_GET['id'];

// Kiểm tra trạng thái phòng
$room = $conn->query("SELECT status FROM rooms WHERE id = $id")->fetch_assoc();

// Nếu phòng đang booked → không cho xóa
if ($room['status'] == 'booked') {
    header("Location: list.php?error=booked");
    exit;
}

// Nếu available thì cho xóa
$conn->query("DELETE FROM rooms WHERE id = $id");
header("Location: list.php");
exit;
