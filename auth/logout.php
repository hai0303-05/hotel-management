<?php
session_start();

// Xoa toan bo session
session_unset();
session_destroy();

// Quay ve trang dang nhap
header("Location: login.php");
exit();
