<?php
// db_connect.php dosyasını include ediyoruz
include 'db_connect.php';

// Kullanıcının username'i
$username = $_GET['username'];

// Kullanıcının user_id'sini bul
$sql_find_user = "SELECT user_id FROM users WHERE username = '$username'";
$result_find_user = $conn->query($sql_find_user);

if ($result_find_user->num_rows > 0) {
    // Kullanıcı bulundu
    $row = $result_find_user->fetch_assoc();
    $user_id = $row["user_id"];

    // Kullanıcının enerji seviyesini kontrol et
    $sql_check_energy = "SELECT energy FROM users WHERE user_id = $user_id";
    $result_check_energy = $conn->query($sql_check_energy);

    if ($result_check_energy->num_rows > 0) {
        // Kullanıcının enerji seviyesini bul
        $row = $result_check_energy->fetch_assoc();
        $energy = $row["energy"];
        
        // Enerji seviyesi 0'dan büyükse, 1 eksilt
        if ($energy > 0) {
            $new_energy = $energy - 1;

            // energy sütununu güncelle
            $sql_update_energy = "UPDATE users SET energy = $new_energy WHERE user_id = $user_id";
            if ($conn->query($sql_update_energy) === TRUE) {
                echo "Enerji seviyesi başarıyla güncellendi.";
            } else {
                echo "Hata: " . $sql_update_energy . "<br>" . $conn->error;
            }
        } else {
            echo "Enerji seviyesi zaten 0'dan küçük.";
        }
    } else {
        echo "Enerji seviyesi bulunamadı.";
    }
} else {
    echo "Kullanıcı bulunamadı.";
}

// Veritabanı bağlantısını kapatıyoruz
$conn->close();
?>
