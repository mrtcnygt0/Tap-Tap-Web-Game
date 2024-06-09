<?php
session_start();
include 'db_connect.php'; // Veritabanı bağlantı bilgilerini içeren dosya

date_default_timezone_set('Europe/Istanbul'); // Türkiye saati için saat dilimini ayarla

$earnedPoints = calculatePoints($botLevel, $lastLogin);
function calculatePoints($botLevel, $lastLogin) {
		date_default_timezone_set('Europe/Istanbul'); // Türkiye saatine göre ayarla
		$currentTime = time();
		$lastLoginTime = strtotime($lastLogin);
		$timeDifference = $currentTime - $lastLoginTime;
		// 43200 saniyeden fazla ise 43200 saniye olarak al
		$timeDifference = min(43200, $timeDifference);

		$earnedPoints = 0;

		// Bot seviyesine ve giriş süresine göre puan hesaplama
		if ($botLevel === 1) {
			$earnedPoints = $timeDifference * 0.25;
		} elseif ($botLevel === 2) {
			$earnedPoints = $timeDifference * 0.55;
		} elseif ($botLevel === 3) {
			$earnedPoints = $timeDifference * 0.83;
		}
		
		return $earnedPoints;
	}
?>
