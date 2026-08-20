<?php
require "config.php";

$user = requireRole("Citizen");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: citizen.php");
    exit();
}

$action = $_POST["action"] ?? "";

if ($action === "profile") {

    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    $stmt = $pdo->prepare(
        "UPDATE users
         SET name = ?, phone = ?, address = ?
         WHERE id = ?"
    );

    $stmt->execute([
        $name,
        $phone,
        $address,
        $user["id"]
    ]);

    addActivity($user["id"], "Updated citizen profile");

    showMessage("Profile updated successfully.");

    header("Location: citizen.php");
    exit();
}

if ($action === "report") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $wasteType = $_POST["waste_type"];
    $location = trim($_POST["location"]);

    $pickupRequested = isset($_POST["pickup_requested"]) ? 1 : 0;
    $recyclable = isset($_POST["recyclable"]) ? 1 : 0;

    $imageName = null;

    if (
        isset($_FILES["waste_image"]) &&
        $_FILES["waste_image"]["error"] === 0
    ) {
        $allowedExtensions = ["jpg", "jpeg", "png"];

        $extension = strtolower(
            pathinfo($_FILES["waste_image"]["name"], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowedExtensions)) {
            showMessage("Only JPG, JPEG and PNG images are allowed.");
            header("Location: citizen.php");
            exit();
        }

        if ($_FILES["waste_image"]["size"] > 2000000) {
            showMessage("Image must be smaller than 2 MB.");
            header("Location: citizen.php");
            exit();
        }

        $imageName = time() . "_" . rand(1000, 9999) . "." . $extension;

        move_uploaded_file(
            $_FILES["waste_image"]["tmp_name"],
            "uploads/" . $imageName
        );
    }

    $stmt = $pdo->prepare(
        "INSERT INTO waste_reports
        (
            citizen_id,
            title,
            description,
            waste_type,
            location,
            waste_image,
            pickup_requested,
            recyclable
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $user["id"],
        $title,
        $description,
        $wasteType,
        $location,
        $imageName,
        $pickupRequested,
        $recyclable
    ]);

    addActivity($user["id"], "Reported a waste issue");

    showMessage("Waste report submitted successfully.");
}

header("Location: citizen.php");
exit();
?>