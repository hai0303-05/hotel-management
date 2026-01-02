<?php
/* ===== LIEN KET VOI INDEX (BAT BUOC) ===== */
if (!defined('IN_INDEX')) die('Access denied');

require_once 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = "";

/* ===== LAY THONG TIN KHACH ===== */
$result = $conn->query("SELECT * FROM customers WHERE id = $id");
$customer = $result ? $result->fetch_assoc() : null;

if (!$customer) {
    die("Khach hang khong ton tai");
}

/* ===== XU LY CAP NHAT ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST['name']);
    $phone   = trim($_POST['phone']);
    $email   = trim($_POST['email']);
    $id_card = trim($_POST['id_card']);

    if ($name === "" || $phone === "") {
        $error = "Ten va so dien thoai khong duoc de trong";
    } else {
        $sql = "
            UPDATE customers
            SET
                name = '$name',
                phone = '$phone',
                email = '$email',
                id_card = '$id_card'
            WHERE id = $id
        ";

        if ($conn->query($sql)) {
            header("Location: index.php?page=customers");
            exit;
        } else {
            $error = "Loi khi cap nhat";
        }
    }
}
?>

<h2>Sua khach hang</h2>

<?php if ($error): ?>
    <p style="color:red"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post">
    Ten:<br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>"><br><br>

    Phone:<br>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>"><br><br>

    Email:<br>
    <input type="text" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>"><br><br>

    CCCD:<br>
    <input type="text" name="id_card" value="<?php echo htmlspecialchars($customer['id_card']); ?>"><br><br>

    <button type="submit">Cap nhat</button>
</form>

<br>
<a href="index.php?page=customers">Quay lai</a>
