<?php
$conn = new mysqli("localhost", "root", "", "hotel_management");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
