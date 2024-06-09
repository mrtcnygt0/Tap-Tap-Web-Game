<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/
  
  
session_start();
include 'db_connect.php';  // Veritabanı bağlantısı ve fonksiyonları içeren dosya

$response = array('success' => false, 'message' => '', 'newLevel' => 0);

if (isset($_POST['level']) && isset($_POST['cost'])) {
    $level = intval($_POST['level']);
    $cost = intval($_POST['cost']);
    $userId = $_SESSION['user_id'];  // Kullanıcı ID'si oturumdan alınır

    // Kullanıcı bilgilerini veritabanından çek
    $query = $db->prepare("SELECT bot_level, points FROM users WHERE id = ?");
    $query->execute([$userId]);
    $userData = $query->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        $currentLevel = intval($userData['bot_level']);
        $userPoints = intval($userData['points']);

        if ($currentLevel < $level) {
            if ($userPoints >= $cost) {
                // Bot seviyesi güncellenir ve puanlar düşülür
                $newPoints = $userPoints - $cost;
                $query = $db->prepare("UPDATE users SET bot_level = ?, points = ? WHERE id = ?");
                if ($query->execute([$level, $newPoints, $userId])) {
                    $response['success'] = true;
                    $response['newLevel'] = $level;
                } else {
                    $response['message'] = 'Veritabanı güncelleme hatası.';
                }
            } else {
                $response['message'] = 'Yeterli puanınız yok.';
            }
        } else {
            $response['message'] = 'Mevcut seviyenizden daha düşük veya aynı seviye satın alınamaz.';
        }
    } else {
        $response['message'] = 'Kullanıcı bulunamadı.';
    }
} else {
    $response['message'] = 'Geçersiz istek.';
}

echo json_encode($response);
?>
