<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit();
}

$name = trim($_POST["name"]);
$email = trim($_POST["email"]);
$phone = trim($_POST["phone"]);
$address = trim($_POST["address"]);
$role = $_POST["role"];
$companyName = trim($_POST["company_name"]);
$password = $_POST["password"];
$confirmPassword = $_POST["confirm_password"];

$allowedRoles = ["Citizen", "Collector", "Recycler"];

if (!in_array($role, $allowedRoles)) {
    showMessage("Invalid account type.");
    header("Location: register.php");
    exit();
}

if ($password !== $confirmPassword) {
    showMessage("Passwords do not match.");
    header("Location: register.php");
    exit();
}

if (strlen($password) < 8) {
    showMessage("Password must contain at least 8 characters.");
    header("Location: register.php");
    exit();
}

if ($role === "Recycler" && empty($companyName)) {
    showMessage("Company name is required.");
    header("Location: register.php");
    exit();
}

$status = "Active";

if ($role === "Collector") {
    $status = "Pending";
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO users
        (name, email, password, phone, address, company_name, role, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $name,
        $email,
        $hashedPassword,
        $phone,
        $address,
        $companyName,
        $role,
        $status
    ]);

    $userId = $pdo->lastInsertId();

    addActivity($userId, "Registered a new $role account");

    if ($role === "Collector") {
        showMessage("Registration successful. Wait for admin approval.");
    } else {
        showMessage("Registration successful. You can now login.");
    }

    header("Location: index.php");

} catch (PDOException $e) {
    showMessage("This email address is already registered.");
    header("Location: register.php");
}

exit();
?>