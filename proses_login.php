<?php
require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

$query = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($query, "s", $username);
mysqli_stmt_execute($query);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($query));

if ($user && $password === $user["password"]) {
    session_regenerate_id(true);
    $_SESSION["login"] = true;
    $_SESSION["username"] = $user["username"];

    // Remember Me: simpan username (bukan password) selama 30 hari
    if (isset($_POST["remember"])) {
        setcookie("remember_username", $username, [
            "expires" => time() + (30 * 24 * 60 * 60), "path" => "/", "httponly" => true, "samesite" => "Lax",
        ]);
    } else {
        setcookie("remember_username", "", ["expires" => time() - 3600, "path" => "/"]);
    }

    set_flash("success", "Selamat datang, " . $user["username"] . "!");
    header("Location: dashboard.php");
    exit;
}

set_flash("error", "Username atau password salah.");
header("Location: index.php");
exit;
