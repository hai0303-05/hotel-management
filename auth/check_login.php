<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 1. Kiem tra da dang nhap chua */
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

/* 2. Ham kiem tra quyen */
function checkRole($roleCanTruyCap) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $roleCanTruyCap) {
        echo "
            <div style='padding:20px'>
                <h2>Ban khong co quyen truy cap!</h2>
                <a href='index.php'>Trang chu</a>
            </div>
        ";
        exit();
    }
}
