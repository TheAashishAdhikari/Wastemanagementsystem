<?php
require "config.php";

$email = "admin@smartwaste.com";
$newPassword = "password";

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "SELECT id FROM users WHERE email = ?"
);

$stmt->execute([$email]);
$admin = $stmt->fetch();

if ($admin) {
    $stmt = $pdo->prepare(
        "UPDATE users
         SET password = ?, role = 'Admin', status = 'Active'
         WHERE email = ?"
    );

    $stmt->execute([$hashedPassword, $email]);

    echo "Admin password successfully reset.";
} else {
    $stmt = $pdo->prepare(
        "INSERT INTO users
        (name, email, password, role, status)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        "System Admin",
        $email,
        $hashedPassword,
        "Admin",
        "Active"
    ]);

    echo "Admin account successfully created.";
}
?>