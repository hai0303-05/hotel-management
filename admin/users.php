<?php
require_once '../auth/check_login.php';
checkRole('admin');
require_once('../config/db.php');

/* ===== THEM TAI KHOAN ===== */
if (isset($_POST['add'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    mysqli_query($conn,
        "INSERT INTO users(username, password, role)
         VALUES('$username', '$password', '$role')"
    );
    header("Location: users.php");
    exit;
}

/* ===== XOA TAI KHOAN ===== */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit;
}

/* ===== SUA TAI KHOAN ===== */
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    mysqli_query($conn,
        "UPDATE users SET username='$username', role='$role' WHERE id=$id"
    );
    header("Location: users.php");
    exit;
}

/* ===== DOI MAT KHAU ===== */
if (isset($_POST['change_pass'])) {
    $id = $_POST['id'];
    $newpass = $_POST['new_password'];

    if (!empty($newpass)) {
        $newpass = md5($newpass);
        mysqli_query($conn,
            "UPDATE users SET password='$newpass' WHERE id=$id"
        );
    }
    header("Location: users.php");
    exit;
}

/* ===== LAY DANH SACH ===== */
$result = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quan ly tai khoan</title>
</head>
<body>

<h2>Quan ly tai khoan nhan vien</h2>

<p>Xin chao Admin: <b><?php echo $_SESSION['username']; ?></b></p>

<!-- ===== FORM THEM ===== -->
<h3>Them tai khoan</h3>
<form method="post">
    Username:
    <input type="text" name="username" required>
    Password:
    <input type="password" name="password" required>
    Role:
    <select name="role">
        <option value="staff">Nhan vien</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit" name="add">Them</button>
</form>

<hr>

<!-- ===== DANH SACH ===== -->
<h3>Danh sach tai khoan</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Doi mat khau</th>
        <th>Hanh dong</th>
    </tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <form method="post">
        <td><?php echo $row['id']; ?></td>

        <td>
            <input type="text" name="username"
                   value="<?php echo $row['username']; ?>">
        </td>

        <td>
            <select name="role">
                <option value="admin"
                    <?php if ($row['role']=='admin') echo 'selected'; ?>>
                    Admin
                </option>
                <option value="staff"
                    <?php if ($row['role']=='staff') echo 'selected'; ?>>
                    Nhan vien
                </option>
            </select>
        </td>

        <td>
            <input type="password" name="new_password"
                   placeholder="Mat khau moi">
        </td>

        <td>
            <input type="hidden" name="id"
                   value="<?php echo $row['id']; ?>">

            <button type="submit" name="update">Sua</button>

            <button type="submit" name="change_pass">
                Doi MK
            </button>

            <?php if ($row['id'] != $_SESSION['user_id']) { ?>
        | <a href="users.php?delete=<?php echo $row['id']; ?>"
             onclick="return confirm('Xoa tai khoan nay?')">
             Xoa
          </a>
    <?php } else { ?>
        | <i>(Tai khoan dang dang nhap)</i>
    <?php } ?>
        </td>
    </form>
</tr>
<?php } ?>

</table>

<p>
    <a href="stats.php">Thong ke</a> |
    <a href="../index.php">Trang chu</a> |
    <a href="../auth/logout.php">Dang xuat</a>
</p>

</body>
</html>
