<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

// Veritabanı bağlantısı için gerekli bilgileri içeren değişkenler
$host = "localhost";
$dbname = "oyun_db";
$username = "root";
$password = "";

try {
    // PDO (PHP Data Objects) kullanarak veritabanına bağlanma
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Bağlantı hatası durumunda hata mesajını göster ve programı sonlandır
    echo "Veritabanı bağlantısı başarısız: " . $e->getMessage();
    die();
}
?>