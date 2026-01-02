<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once 'config/db.php';

$name    = $_POST['name'] ?? '';
$phone   = $_POST['phone'] ?? '';
$email   = $_POST['email'] ?? '';
$id_card = $_POST['id_card'] ?? '';
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($name === '') {
        $errors[] = "Ten bat buoc";
    }

    if ($phone === '') {
        $errors[] = "SDT bat buoc";
    }

    if ($id_card !== '') {
        $id_card = trim($id_card);
        $check = $conn->query("
            SELECT id FROM customers 
            WHERE id_card = '$id_card'
        ");
        if ($check && $check->num_rows > 0) {
            $errors[] = "CCCD da ton tai, khong the them";
        }
    }

    if (!$errors) {
        $stmt = $conn->prepare("
            INSERT INTO customers (name, phone, email, id_card)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $name, $phone, $email, $id_card);
        $stmt->execute();

        header("Location: index.php?page=customers");
        exit;
    }
}
?>

<h2>Them khach hang</h2>

<form method="post">
    Ten:<br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"><br><br>

    SDT:<br>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>"><br><br>

    Email:<br>
    <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>"><br><br>

    CCCD (khong duoc trung):<br>
    <input type="text" name="id_card" value="<?php echo htmlspecialchars($id_card); ?>"><br><br>

    <button type="submit">Luu</button>
</form>

<?php foreach ($errors as $e): ?>
    <p style="color:red"><?php echo $e; ?></p>
<?php endforeach; ?>

<br>
<a href="index.php?page=customers">Quay lai danh sach</a>
