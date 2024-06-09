<?php

include 'db_connect.php'; // Veritabanı bağlantı bilgilerini içeren dosya

date_default_timezone_set('Europe/Istanbul'); // Türkiye saati için saat dilimini ayarla

if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    $currentTime = date('Y-m-d H:i:s'); // Şu anki tarihi ve saati al
    // Kullanıcının son giriş zamanını güncelle
    $stmt = $conn->prepare("UPDATE users SET last_login = :last_loginn WHERE username = :username");
    $stmt->bindParam(':last_loginn', $currentTime);
    $stmt->bindParam(':username', $user);
    $success = $stmt->execute(); // Sorguyu çalıştır ve başarılı olup olmadığını kontrol et
    if (!$success) {
        // SQL sorgusu başarısız olduysa hata mesajını göster ve işlemi durdur
        die("SQL hatası: " . $stmt->errorInfo()[2]);
    }
}
?>
