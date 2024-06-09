<?php
$totalUsersQuery = "SELECT COUNT(*) as totalUsers FROM users";
$totalScoreQuery = "SELECT SUM(score) as totalScore FROM users";

$totalUsersResult = $conn->query($totalUsersQuery);
$totalScoreResult = $conn->query($totalScoreQuery);

$totalUsers = $totalUsersResult->fetch_assoc()['totalUsers'];
$totalScore = $totalScoreResult->fetch_assoc()['totalScore'];
?>

<h2>Genel Bakış</h2>
<div>
    <h3>İstatistikler</h3>
    <p>Toplam Kullanıcı Sayısı: <?php echo $totalUsers; ?></p>
    <p>Toplanmış Total Puan: <?php echo $totalScore; ?></p>
</div>
