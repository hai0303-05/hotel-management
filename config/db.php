<?php
$conn = new mysqli("localhost", "root", "", "hotel_management");

if ($conn->connect_error) {
    die("Ket noi that bai: " . $conn->connect_error);
}
