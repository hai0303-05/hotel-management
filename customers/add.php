<?php
require_once "../config/db.php";


$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST["name"]);
    $phone   = trim($_POST["phone"]);
    $email   = trim($_POST["email"]);
    $id_card = trim($_POST["id_card"]);

    if ($name == "" || $phone == "") {
        $error = "Ten va so dien thoai khong duoc de trong";
    } else {
        $sql = "INSERT INTO customers (name, phone, email, id_card, status)
                VALUES ('$name', '$phone', '$email', '$id_card', 'dat_truoc')";
        if ($conn->query($sql)) {
            header("Location: list.php");
            exit();
        } else {
            $error = "Loi khi them khach hang";
        }
    }
}
?>
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'];
    $phone   = $_POST['phone'];
    $email   = $_POST['email'];
    $id_card = $_POST['id_card'];
    
    $sql = "INSERT INTO customers (name, phone, email, id_card)
            VALUES ('$name', '$phone', '$email', '$id_card')";

    if ($conn->query($sql) === TRUE) {
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


<?php if ($error != "") echo "<p style='color:red'>$error</p>"; ?>

<form method="post">
    Ten:<br>
    <input type="text" name="name"><br><br>

    Phone:<br>
    <input type="text" name="phone"><br><br>

    Email:<br>
    <input type="text" name="email"><br><br>

    CCCD:<br>
    <input type="text" name="id_card"><br><br>

    <button type="submit">Luu</button>
    <a href="list.php">Quay lai</a>
</form>
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
