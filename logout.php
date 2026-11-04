<?php
session_start();

// Menghapus semua data session
$_SESSION = [];

// Menghapus cookie session
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), "", time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
}

// Menghapus cookie Remember Me
setcookie("remember_username", "", ["expires" => time() - 3600, "path" => "/"]);

session_destroy();

// Kembali ke halaman login
header("Location: index.php");
exit;
