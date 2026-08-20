<?php
session_start();

$host = "localhost";
$port = "8889";
$dbname = "smart_waste_db";
$username = "root";
$password = "root";
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function clean($value)
{
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

function showMessage($text)
{
    $_SESSION["message"] = $text;
}

function currentUser()
{
    global $pdo;

    if (!isset($_SESSION["user_id"])) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION["user_id"]]);

    return $stmt->fetch();
}

function requireRole($role)
{
    $user = currentUser();

    if (!$user || $user["role"] !== $role) {
        header("Location: index.php");
        exit();
    }

    return $user;
}

function addActivity($userId, $activity)
{
    global $pdo;

    $stmt = $pdo->prepare(
        "INSERT INTO activities (user_id, activity) VALUES (?, ?)"
    );

    $stmt->execute([$userId, $activity]);
}

function dashboardPage($role)
{
    if ($role === "Citizen") {
        return "citizen.php";
    }

    if ($role === "Collector") {
        return "collector.php";
    }

    if ($role === "Recycler") {
        return "recycler.php";
    }

    return "admin.php";
}
?>