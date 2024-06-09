<?php
session_start(); // Oturumu başlat

// Veritabanı bağlantısı için gerekli bilgileri içeren değişkenler
include 'db_connect.php'; // Bağlantı yapıldıktan sonra

// Kullanıcı adını al
$username = $_SESSION['username']; // Kullanıcı adını oturum değişkeninden alın

// Gelen JSON verisini al
$data = json_decode(file_get_contents('php://input'), true);

// Enerji miktarını al
$energy = isset($data['energy']) ? $data['energy'] : null;

if ($energy !== null) {
    try {
        // Kullanıcının enerji miktarını güncelle
        $sql = "UPDATE users SET energy = :energy WHERE username = :username"; // SQL sorgusu parametreli olarak kullanıldı
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':energy', $energy, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(array("success" => true, "message" => "Enerji başarıyla güncellendi"));
    } catch (PDOException $e) {
        echo json_encode(array("success" => false, "message" => "Enerji güncelleme hatası: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Enerji değeri bulunamadı"));
}
?>
