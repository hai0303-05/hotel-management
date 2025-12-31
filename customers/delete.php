<?php
require_once "../config/db.php";

$id = $_GET["id"] ?? 0;

$conn->query("DELETE FROM customers WHERE id = $id");

header("Location: list.php");
exit();
