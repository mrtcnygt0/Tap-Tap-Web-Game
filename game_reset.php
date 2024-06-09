<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

// db_connect.php dosyasını include ediyoruz
include 'db_connect.php';

// Veritabanı bağlantısını oluşturuyoruz
$conn = new mysqli($host, $username, $password, $dbname);

// Bağlantıyı kontrol ediyoruz
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Tüm kullanıcıların game sütununu 10 olarak güncelliyoruz
$sql = "UPDATE users SET game = 10";
if ($conn->query($sql) === TRUE) {
    echo "Tüm kullanıcıların game sütunu başarıyla 10 yapıldı.";
} else {
    echo "Hata: " . $sql . "<br>" . $conn->error;
}

// Veritabanı bağlantısını kapatıyoruz
$conn->close();
?>
