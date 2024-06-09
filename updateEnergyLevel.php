<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    $data = json_decode(file_get_contents('php://input'), true);
    $energyLevel = (int)$data['energy_level'];

    try {
        $stmt = $conn->prepare("UPDATE users SET energy_level = :energy_level WHERE username = :username");
        $stmt->bindParam(':energy_level', $energyLevel);
        $stmt->bindParam(':username', $user);
        $stmt->execute();

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'energy seviyesi güncellenirken bir hata oluştu']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Kullanıcı oturumu bulunamadı']);
}
?>
