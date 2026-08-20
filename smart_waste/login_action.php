<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$email = trim($_POST["email"]);
$password = $_POST["password"];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

$user = $stmt->fetch();

if (!$user || !password_verify($password, $user["password"])) {
    showMessage("Incorrect email or password.");
    header("Location: index.php");
    exit();
}

if ($user["status"] !== "Active") {
    showMessage("Your account is waiting for approval or has been suspended.");
    header("Location: index.php");
    exit();
}

$_SESSION["user_id"] = $user["id"];

addActivity($user["id"], "Accessed account");

header("Location: " . dashboardPage($user["role"]));
exit();
?>