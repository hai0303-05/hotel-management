<?php
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
        echo "Loi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Them khach hang</title>
</head>
<body>

<h2>Them khach hang</h2>

<form method="post">
    <label>Ten:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Phone:</label><br>
    <input type="text" name="phone"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email"><br><br>

    <label>CCCD:</label><br>
    <input type="text" name="id_card"><br><br>

    <button type="submit">Luu</button>
</form>

<br>
<a href="list.php">Quay lai danh sach</a>

</body>
</html>
