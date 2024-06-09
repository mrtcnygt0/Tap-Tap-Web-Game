<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$username = $_SESSION['username'];

$data = json_decode(file_get_contents('php://input'), true);
$new_score = $data['score'];

$servername = "localhost";
$dbname = "oyun_db";
$dbuser = "root";
$dbpass = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("UPDATE users SET score = :score WHERE username = :username");
    $stmt->bindParam(':score', $new_score);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn = null;
?>
