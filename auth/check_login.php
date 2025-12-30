<?php
session_start();

/* 1. Kiem tra da dang nhap chua */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* 2. Ham kiem tra quyen */
function checkRole($roleCanTruyCap) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $roleCanTruyCap) {
        echo "Ban khong co quyen truy cap!";
        exit();
    }
}
