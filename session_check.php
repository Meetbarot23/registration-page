<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['department'])) {
    header("Location: approval_login.html");
    exit();
}
?> 