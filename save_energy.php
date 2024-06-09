<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

include 'db_connect.php';

// JSON'dan gelen veriyi al
$data = json_decode(file_get_contents('php://input'), true);
$energy = $data['energy'];

// Kullanıcı ID'sini al (örneğin, oturumdan)
$username = $_SESSION['username'];

// Enerjiyi güncelle
$sqlUpdate = "UPDATE users SET energy = $energy WHERE id = $username";
$conn->query($sqlUpdate);

$conn->close();
?>
