<?php
require_once __DIR__ . '/../config/db.php';

$id = (int)$_GET['id'];
$customer = $conn->query("SELECT * FROM customers WHERE id=$id")->fetch_assoc();

if (!$customer) die('Khách không tồn tại');

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $id_card = $conn->real_escape_string(trim($_POST['id_card']));

    if ($name == '' || $phone == '') {
        $error = 'Tên và số điện thoại bắt buộc';
    } else {
        $sql = "UPDATE customers SET name='$name', phone='$phone', 
                email=NULLIF('$email',''), id_card=NULLIF('$id_card','') WHERE id=$id";
        if ($conn->query($sql)) {
            header('Location: list.php');
            exit();
        } else { $error = 'Lỗi cập nhật'; }
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Sửa khách hàng</title></head>
<body>
    <h2>Sửa khách hàng</h2>
    <form method="post">
        Tên *<br><input type="text" name="name" value="<?= $customer['name'] ?>"><br><br>
        Phone *<br><input type="text" name="phone" value="<?= $customer['phone'] ?>"><br><br>
        Email<br><input type="email" name="email" value="<?= $customer['email'] ?>"><br><br>
        CCCD<br><input type="text" name="id_card" value="<?= $customer['id_card'] ?>"><br><br>
        <button type="submit">Cập nhật</button>
        <a href="list.php">Quay lại</a>
    </form>
</body>
</html>