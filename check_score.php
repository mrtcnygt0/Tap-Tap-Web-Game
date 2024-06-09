<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

session_start();

// Eğer kullanıcı oturumu açıksa
if (isset($_SESSION['username'])) {
    // Kullanıcı veritabanından puanı al
    $user = $_SESSION['username'];
    // Veritabanı bağlantısı ve diğer gerekli işlemler yapıldı varsayılsın
    include 'db_connect.php'; // Veritabanı bağlantı bilgilerini içeren dosya

    // Kullanıcının puanını veritabanından al
    $stmt = $conn->prepare("SELECT score FROM users WHERE username = :username");
    $stmt->bindParam(':username', $user);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kullanıcının puanını JSON olarak geri döndür
    echo json_encode(['status' => 'success', 'score' => $userData['score']]);
} else {
    // Oturum açılmamışsa hata döndür
    echo json_encode(['status' => 'error', 'message' => 'Session not set']);
}
?>
