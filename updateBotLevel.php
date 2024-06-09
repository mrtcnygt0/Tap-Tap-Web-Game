<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

session_start();
require_once 'db_connect.php';

if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    $data = json_decode(file_get_contents('php://input'), true);
    $botLevel = (int)$data['bot_level'];

    try {
        $stmt = $conn->prepare("UPDATE users SET bot_level = :bot_level WHERE username = :username");
        $stmt->bindParam(':bot_level', $botLevel);
        $stmt->bindParam(':username', $user);
        $stmt->execute();

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Bot seviyesi güncellenirken bir hata oluştu']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Kullanıcı oturumu bulunamadı']);
}
?>
