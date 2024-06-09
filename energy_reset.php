<?php
// db_connect.php dosyasını include ediyoruz
include 'db_connect.php';

// Veritabanı bağlantısını oluşturuyoruz
$conn = new mysqli($host, $username, $password, $dbname);

// Bağlantıyı kontrol ediyoruz
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Tüm kullanıcıların energy sütununu 5000 olarak güncelliyoruz
$sql = "UPDATE users SET energy = 100000";
if ($conn->query($sql) === TRUE) {
    echo "Tüm kullanıcıların energy sütunu başarıyla 100000 yapıldı.";
} else {
    echo "Hata: " . $sql . "<br>" . $conn->error;
}

// Veritabanı bağlantısını kapatıyoruz
$conn->close();
?>
