<?php
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'];
    $phone   = $_POST['phone'];
    $email   = $_POST['email'];
    $id_card = $_POST['id_card'];

    $sql = "INSERT INTO customers (name, phone, email, id_card)
            VALUES ('$name', '$phone', '$email', '$id_card')";

    if ($conn->query($sql)) {
        header("Location: list.php");
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Customer</title>
</head>
<body>

<h2>Thêm khách hàng</h2>

<form method="post">
    <p>
        Name:<br>
        <input type="text" name="name" required>
    </p>
    <p>
        Phone:<br>
        <input type="text" name="phone">
    </p>
    <p>
        Email:<br>
        <input type="email" name="email">
    </p>
    <p>
        ID Card:<br>
        <input type="text" name="id_card">
    </p>
    <button type="submit">Thêm</button>
</form>

<a href="list.php">Quay lại</a>

</body>
</html>
//da
