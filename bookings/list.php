<?php
require_once "../config/db.php";

$phone = $_GET['phone'] ?? '';

if (strlen($phone) < 3) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, name, phone, id_card
        FROM customers
        WHERE phone LIKE ?
        LIMIT 5";

$stmt = $conn->prepare($sql);
$like = "%$phone%";
$stmt->bind_param("s", $like);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
