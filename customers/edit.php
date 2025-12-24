<?php
require_once "../config/db.php";

$id = $_GET['id'];

$sql = "SELECT * FROM customers WHERE id = $id";
$result = $conn->query($sql);
$customer = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'];
    $phone   = $_POST['phone'];
    $email   = $_POST['email'];
    $id_card = $_POST['id_card'];

    $sql = "UPDATE customers 
            SET name='$name', phone='$phone', email='$email', id_card='$id_card'
            WHERE id=$id";

    if ($conn->query($sql)) {
        header("Location: list.php");
        exit();
    } else {
        echo "Loi: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
</head>
<body>

<h2>Sửa khách hàng</h2>

<form method="post">
    <p>
        Name:<br>
        <input type="text" name="name" value="<?php echo $customer['name']; ?>" required>
    </p>
    <p>
        Phone:<br>
        <input type="text" name="phone" value="<?php echo $customer['phone']; ?>">
    </p>
    <p>
        Email:<br>
        <input type="email" name="email" value="<?php echo $customer['email']; ?>">
    </p>
    <p>
        ID Card:<br>
        <input type="text" name="id_card" value="<?php echo $customer['id_card']; ?>">
    </p>
    <button type="submit">Cập nhật</button>
</form>

<a href="list.php">Quay lại</a>

</body>
</html>
