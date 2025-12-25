<?php
require_once "../config/db.php";

$id = $_GET['id'];

$sql = "DELETE FROM customers WHERE id = $id";

if ($conn->query($sql)) {
    header("Location: list.php");
    exit();
} else {
    echo "Lỗi: " . $conn->error;
}
//da 
