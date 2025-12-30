<?php
require_once '../auth/check_login.php';
checkRole('admin');
require_once '../config/db.php';

$error = '';

/* THEM */
if (isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    if ($username == '' || $password == '') {
        $error = "Khong duoc de trong username hoac password";
    } else {
        $check = mysqli_query($conn,
            "SELECT id FROM users WHERE username='$username'"
        );

        if (mysqli_num_rows($check) > 0) {
            $error = "Username da ton tai";
        } else {
            $password = md5($password);
            mysqli_query($conn,
                "INSERT INTO users(username,password,role)
                 VALUES('$username','$password','$role')"
            );
            header("Location: users.php");
            exit;
        }
    }
}

/* XOA */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit;
}

/* SUA */
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $new_password = trim($_POST['new_password']);

    if ($username == '') {
        $error = "Username khong duoc de trong";
    } else {
        if ($new_password != '') {
            $new_password = md5($new_password);
            mysqli_query($conn,
                "UPDATE users 
                 SET username='$username', role='$role', password='$new_password'
                 WHERE id=$id"
            );
        } else {
            mysqli_query($conn,
                "UPDATE users 
                 SET username='$username', role='$role'
                 WHERE id=$id"
            );
        }
        header("Location: users.php");
        exit;
    }
}

$result = mysqli_query($conn, "SELECT * FROM users");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quan ly tai khoan</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">
    <h2 class="page-title">Quan ly tai khoan nhan vien</h2>

    <p>Xin chao Admin: <b><?php echo $_SESSION['username']; ?></b></p>

    <?php if ($error != '') { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <h3>Them tai khoan</h3>
    <form method="post">
        <div class="form-group">
            <input type="text" name="username" class="form-control" placeholder="Username">
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password">
        </div>
        <div class="form-group">
            <select name="role" class="form-control">
                <option value="staff">Nhan vien</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Them</button>
    </form>

    <hr>

    <table class="table">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Mat khau moi</th>
            <th>Hanh dong</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
        <form method="post">
            <td><?php echo $row['id']; ?></td>
            <td><input type="text" name="username" class="form-control" value="<?php echo $row['username']; ?>"></td>
            <td>
                <select name="role" class="form-control">
                    <option value="admin" <?php if ($row['role']=='admin') echo 'selected'; ?>>Admin</option>
                    <option value="staff" <?php if ($row['role']=='staff') echo 'selected'; ?>>Nhan vien</option>
                </select>
            </td>
            <td><input type="password" name="new_password" class="form-control"></td>
            <td>
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="update" class="btn btn-primary">Sua</button>

                <?php if ($row['id'] != $_SESSION['user_id']) { ?>
                    <a href="users.php?delete=<?php echo $row['id']; ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Xoa tai khoan nay?')">Xoa</a>
                <?php } ?>
            </td>
        </form>
        </tr>
        <?php } ?>
    </table>

    <a href="../index.php">Trang chu</a>
</div>

</body>
</html>
