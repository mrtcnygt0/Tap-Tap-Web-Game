<?php
session_start();
include 'db_connect.php';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['level'])) {
        $newLevel = (int)$data['level'];
        $user = $_SESSION['username'];

        try {
            $stmt = $conn->prepare("UPDATE users SET level = :level WHERE username = :username");
            $stmt->bindParam(':level', $newLevel, PDO::PARAM_INT);
            $stmt->bindParam(':username', $user, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No rows updated']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method or session']);
}
?>
