<?php
require_once "../config/db.php";

$id = $_GET["id"] ?? 0;
$error = "";

$result = $conn->query("SELECT * FROM customers WHERE id = $id");
$customer = $result->fetch_assoc();

if (!$customer) {
    die("Khach hang khong ton tai");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST["name"]);
    $phone   = trim($_POST["phone"]);
    $email   = trim($_POST["email"]);
    $id_card = trim($_POST["id_card"]);

    if ($name == "" || $phone == "") {
        $error = "Ten va so dien thoai khong duoc de trong";
    } else {
        $sql = "UPDATE customers 
                SET name='$name', phone='$phone', email='$email', id_card='$id_card'
                WHERE id=$id";
        if ($conn->query($sql)) {
            header("Location: list.php");
            exit();
        } else {
            $error = "Loi khi cap nhat";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sua khach hang</title>
</head>
<body>

<h2>Sua khach hang</h2>

<?php if ($error != "") echo "<p style='color:red'>$error</p>"; ?>

<form method="post">
    Ten:<br>
    <input type="text" name="name" value="<?php echo $customer['name']; ?>"><br><br>

    Phone:<br>
    <input type="text" name="phone" value="<?php echo $customer['phone']; ?>"><br><br>

    Email:<br>
    <input type="text" name="email" value="<?php echo $customer['email']; ?>"><br><br>

    CCCD:<br>
    <input type="text" name="id_card" value="<?php echo $customer['id_card']; ?>"><br><br>

    <button type="submit">Cap nhat</button>
    <a href="list.php">Quay lai</a>
</form>

</body>
</html>
