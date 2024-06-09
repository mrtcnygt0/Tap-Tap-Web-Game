<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

include 'db_connect.php'; // Veritabanı bağlantısını sağlayan dosya

$username = $_SESSION['username'];
$score = intval($_POST['score']);

$query = $conn->prepare("SELECT score FROM users WHERE username = :username");
$query->execute([':username' => $username]);
$currentScore = $query->fetchColumn();

$newScore = $currentScore + $score;

$update = $conn->prepare("UPDATE users SET score = :score WHERE username = :username");
$update->execute([':score' => $newScore, ':username' => $username]);

// 'game' sütununu 1 eksiltmek için
$updateGame = $conn->prepare("UPDATE users SET game = game - 1 WHERE username = :username");
$updateGame->execute([':username' => $username]);
?>
