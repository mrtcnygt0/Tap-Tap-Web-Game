<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$username = $_SESSION['username'];

// Kullanıcının mevcut puanını ve task_insta durumunu al
$sql = "SELECT score, task_davet FROM users WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':username', $username);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    if ($result['task_davet'] == 0) {
        $new_score = $result['score'] + 10000;
        $sql = "UPDATE users SET score=:new_score, task_davet=1 WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':new_score', $new_score);
        $stmt->bindValue(':username', $username);
        if ($stmt->execute()) {
            echo "success";
			echo "<script>location.reload();</script>";
        } else {
            echo "error";
        }
    } else {
        echo "already_done";
    }
	if ($result['task_davet'] == 1) {
        $new_score = $result['score'] + 30000;
        $sql = "UPDATE users SET score=:new_score, task_davet=2 WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':new_score', $new_score);
        $stmt->bindValue(':username', $username);
        if ($stmt->execute()) {
            echo "success";
			echo "<script>location.reload();</script>";
        } else {
            echo "error";
        }
    } else {
        echo "already_done";
    }
	if ($result['task_davet'] == 2) {
        $new_score = $result['score'] + 100000;
        $sql = "UPDATE users SET score=:new_score, task_davet=3 WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':new_score', $new_score);
        $stmt->bindValue(':username', $username);
        if ($stmt->execute()) {
            echo "success";
			echo "<script>location.reload();</script>";
        } else {
            echo "error";
        }
    } else {
        echo "already_done";
    }
} else {
    echo "user_not_found";
}

$conn = null;
?>
