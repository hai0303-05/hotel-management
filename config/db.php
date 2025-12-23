<?php
$conn = mysqli_connect("localhost", "root", "", "hotel_management");

if (!$conn) {
    die("Loi ket noi DB: " . mysqli_connect_error());
}
