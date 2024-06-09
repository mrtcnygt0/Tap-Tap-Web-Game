<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

include 'db_connect.php'; // Veritabanı bağlantısını sağlayan dosya

$username = $_SESSION['username'];
$energy = intval($_POST['energy']);

$query = $conn->prepare("SELECT energy FROM users WHERE username = :username");
$query->execute([':username' => $username]);
$currentEnergy = $query->fetchColumn();

$newEnergy = $currentEnergy + $energy;

$update = $conn->prepare("UPDATE users SET energy = :energy WHERE username = :username");
$update->execute([':energy' => $newEnergy, ':username' => $username]);

// Yeni enerji değerini döndür
echo $newEnergy;
?>
