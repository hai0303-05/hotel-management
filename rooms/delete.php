<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once "../config/db.php";

$id = $_GET['id'] ?? 0;

$room = $conn->query("SELECT status FROM rooms WHERE id = $id")->fetch_assoc();

if ($room && $room['status'] === 'booked') {
    header("Location: index.php?page=rooms");
    exit;
}

$conn->query("DELETE FROM rooms WHERE id = $id");

header("Location: index.php?page=rooms");
<<<<<<< HEAD
<<<<<<< Updated upstream
exit;
=======
exit;
>>>>>>> Stashed changes
=======
exit;
>>>>>>> rescue-full-code
