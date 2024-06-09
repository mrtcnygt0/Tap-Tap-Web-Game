<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

session_start();
include 'db_connect.php';

date_default_timezone_set('Europe/Istanbul');

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT game, energy, energy_level, user_ref, score, level, ref_sayisi, bot_level, last_login, task_insta, task_twitter, task_spotify, task_davet FROM users WHERE username = :username");
    $stmt->bindParam(':username', $user);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($userData) {
        $userScore = $userData['score'];
        $userLevel = $userData['level'];
        $ref_sayisi = $userData['ref_sayisi'];
        $user_ref = $userData['user_ref'];
        $lastLogin = $userData['last_login'];
        $botLevel = intval($userData['bot_level']);
        $taskInsta = $userData['task_insta'];
		$taskTwitter = $userData['task_twitter'];
		$taskSpotify = $userData['task_spotify'];
		$taskDavet = $userData['task_davet'];
		$energyLimit = $userData['energy'];
		$energyLevel = $userData['energy_level'];
		$game = $userData['game'];
        error_reporting(E_ALL);

        $currentTime = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("UPDATE users SET last_login = :last_login WHERE username = :username");
        $stmt->bindParam(':last_login', $currentTime);
        $stmt->bindParam(':username', $user);
        if (!$stmt->execute()) {
            die("SQL error: " . $stmt->errorInfo()[2]);
        }

        function calculatePoints($botLevel, $lastLogin) {
            date_default_timezone_set('Europe/Istanbul');
            $currentTimex = time();
            $lastLoginTime = strtotime($lastLogin);
            $timeDifference = min(43200, $currentTimex - $lastLoginTime);
            switch ($botLevel) {
                case 1:
                    return $timeDifference * 0.25;
                case 2:
                    return $timeDifference * 0.55;
                case 3:
                    return $timeDifference * 0.83;
                default:
                    return 0;
            }
        }
		
		function calculateEnergy($energyLevel, $lastLogin, $energyLimit) {
            date_default_timezone_set('Europe/Istanbul');
            $currentTimey = time();
            $lastLoginTime = strtotime($lastLogin);
            $timeDifference = $currentTimey - $lastLoginTime;
			$kazanilmisEnerji =0;
			$yedekEnergy=$energyLimit;
			
			if($energyLevel==1){
				$kazanilmisEnerji=$timeDifference * 2;
				$yedekEnergy +=$kazanilmisEnerji;
				if($yedekEnergy>1000){
					$kazanilmisEnerji=1000-$energyLimit;
				}
				return $kazanilmisEnerji;
			}
			else if($energyLevel==2){
				$kazanilmisEnerji=$timeDifference * 3;
				$yedekEnergy +=$kazanilmisEnerji;
				if($yedekEnergy>1000){
					$kazanilmisEnerji=1000-$energyLimit;
				}
				return $kazanilmisEnerji;
			}
			else if($energyLevel==3){
				$kazanilmisEnerji=$timeDifference * 4;
				$yedekEnergy +=$kazanilmisEnerji;
				if($yedekEnergy>1000){
					$kazanilmisEnerji=1000-$energyLimit;
				}
				return $kazanilmisEnerji;
			}
			else {
				$kazanilmisEnerji=$timeDifference * 1;
				$yedekEnergy +=$kazanilmisEnerji;
				if($yedekEnergy>1000){
					$kazanilmisEnerji=1000-$energyLimit;
				}
				return $kazanilmisEnerji;
			}
        }
		
		$earnedEnergy = calculateEnergy($energyLevel, $lastLogin, $energyLimit);
        $energyLimit += $earnedEnergy;
		
		
		$earnedPoints = calculatePoints($botLevel, $lastLogin);
        $userScore += $earnedPoints;
		
		

        $stmt = $conn->prepare("UPDATE users SET score = :score WHERE username = :username");
        $stmt->bindParam(':score', $userScore);
        $stmt->bindParam(':username', $user);
        $stmt->execute();
		
		
		$stmt = $conn->prepare("UPDATE users SET energy = :energy WHERE username = :username");
        $stmt->bindParam(':energy', $energyLimit);
        $stmt->bindParam(':username', $user);
        $stmt->execute();


        $stmt = $conn->prepare("SELECT u.username, u.score FROM users u JOIN users r ON u.where_ref = r.user_ref WHERE r.username = :username");
        $stmt->bindParam(':username', $user);
        $stmt->execute();
        $referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("SELECT username, score FROM users ORDER BY score DESC");
        $stmt->execute();
        $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    $userScore = 0;
    $userLevel = 0;
    $ref_sayisi = 0;
    $referrals = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['score'])) {
        $newScore = (int)$data['score'];
        $stmt = $conn->prepare("UPDATE users SET score = :score WHERE username = :username");
        $stmt->bindParam(':score', $newScore);
        $stmt->bindParam(':username', $user);
        $stmt->execute();
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
    exit;
}

include 'updatelastlogin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>TAP-TAP Game</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="manifest" href="/manifest.json">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="coindanismani.com">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link href="/pwa/img/favicon_128.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
	<link href="/pwa/img/favicon_128.png" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
	<link href="/pwa/img/favicon_128.png" media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
	<link href="/pwa/img/favicon_128.png" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
	<link href="/pwa/img/favicon_128.png" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
	<link href="/pwa/img/favicon_128.png" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
	<link href="/pwa/img/favicon_128.png" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
	<link rel="apple-touch-icon" sizes="128x128" href="/pwa/img/128x128.png">
	<link rel="apple-touch-icon-precomposed" sizes="128x128" href="/pwa/img/favicon_128.png">
	<link rel="icon" sizes="192x192" href="/pwa/img/favicon_192.png">
	<link rel="icon" sizes="128x128" href="/pwa/img/favicon_128.png">
	
	<script>
        if ('serviceWorker' in navigator) {
        	window.addEventListener('load', function () {
        		navigator.serviceWorker.register('/sw.js?v=3');
        	});
        }
		
		const installButton = document.getElementById('install-app');
		let beforeInstallPromptEvent
		window.addEventListener("beforeinstallprompt", function(e) {
			e.preventDefault();
			beforeInstallPromptEvent = e
			installButton.style.display = 'block'
			installButton.addEventListener("click", function() {
				e.prompt();
			});
			installButton.hidden = false;
		});
		installButton.addEventListener("click", function() {
			beforeInstallPromptEvent.prompt();
		});
    </script>

	<style>
		/* Buton Stili */
		.glow-button {
			display: inline-block;
			padding: 10px 20px;
			font-size: 16px;
			font-weight: bold;
			text-transform: uppercase;
			text-align: center;
			color: #000;
			background-color: #ffcc00; /* Renk */
			border: none; /* Kenarlık Yok */
			border-radius: 5px; /* Köşe Yarıçapı */
			box-shadow: 0 0 10px rgba(76, 175, 80, 0.8); /* Glow Efekti */
			transition: all 0.3s ease; /* Geçiş Efekti */
			position: relative; /* Göreceli konumlandırma */
			overflow: hidden; /* Taşan kısımları gizle */
		}

		/* Butonun Üzerine Gelindiğinde */
		.glow-button:hover {
			background-color: #45a049; /* Hover Renk */
			box-shadow: 0 0 20px rgba(76, 175, 80, 0.9); /* Daha Belirgin Glow */
		}

		/* Butonun Aktif Olduğunda */
		.glow-button:active {
			background-color: #3e8e41; /* Aktif Renk */
			box-shadow: none; /* Gölgeyi Kaldır */
		}
		/* İkon Stili */
		.glow-button i {
			position: absolute;
			left: -30px; /* İkonun butonun solundan ne kadar uzakta olacağı */
			top: 50%; /* İkonu butonun ortasına hizalamak için */
			transform: translateY(-50%);
			font-size: 20px;
		}
	</style>
</head>
<body>
	<button id="install-app" style="display: none">Uygulamayı Yükle</button>
    <div id="gameContent" class="game-container">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="info">
				<h2>  ID: <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
				<div class="puan-text" id="score">
					<i class="fas fa-coins" style="color: white;"></i><?php echo number_format($userScore, 2); ?>
				</div>
				<p class="icon-text"><i class="fas fa-hand-point-up"></i> : <?php echo htmlspecialchars($userLevel); ?></p>
				<p class="icon-text"><i class="fas fa-robot"></i> : <?php echo htmlspecialchars($botLevel); ?></p>

				<?php
				// $botLevel değerine göre uygun metni belirle
				switch ($botLevel) {
					case 0:
						$text = "0/s";
						break;
					case 1:
						$text = "0,25/s";
						break;
					case 2:
						$text = "0,55/s";
						break;
					case 3:
						$text = "0,83/s";
						break;
					default:
						$text = "0/s"; // Varsayılan olarak 0 değeri
				}
				?>
				<p class="icon-text"><i class="fas fa-plus"></i> : <?php echo $text; ?></p>
				<p class="icon-text"><i class="fas fa-user"></i> : <?php echo htmlspecialchars($ref_sayisi); ?></p>
				<?php
				// Türkçe ay isimlerini tanımla
				$turkce_aylar = array(
					'01' => 'Ocak',
					'02' => 'Şubat',
					'03' => 'Mart',
					'04' => 'Nisan',
					'05' => 'Mayıs',
					'06' => 'Haziran',
					'07' => 'Temmuz',
					'08' => 'Ağustos',
					'09' => 'Eylül',
					'10' => 'Ekim',
					'11' => 'Kasım',
					'12' => 'Aralık'
				);

				// Son giriş tarihini normal hale çevirip yazdır
				$son_giris = date("d", strtotime($lastLogin)) . ' ' . $turkce_aylar[date("m", strtotime($lastLogin))] . ' ' . date("H:i", strtotime($lastLogin));
				?>
				<p class="icon-text"><i class="fas fa-star"></i> : <?php echo $game; ?></p>
				<p class="icon-text"><i class="fas fa-door"></i>  <?php echo "Son Giriş: " . $son_giris; ?></p>
			</div>

            <div class="circle-container">
				<div class="circle" style="background-color: black;">
					<img id="dynamicImage" src="images/T.png" alt="Description of your image" style="max-width: 100%; max-height: 100%; object-fit: contain;">
				</div>
			</div>
			
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
			
            <span id="tapText">TAP-TAP</span>
			<?php
			// Yüzdeyi hesapla ve formatla
			$percent = number_format(($userLevel / 8) * 100, 2);
			?>
			<div class="level-bar">
				<div class="level-progress" id="energyBar" style="width: <?php echo ($energyLimit / 1000) * 100; ?>%;"></div>
			</div>
			<div class="level-text" id="energy">
				<i class="fas fa-bolt"></i><?php echo number_format($energyLimit, 0, '.', ','); ?>
			</div>
			<?php if ($game > 0): ?>
			<a href="game.php">
				<img src="images/gameButton.png" alt="Play Game" style="width: 180px; height: 50px; margin-top: 0px; box-shadow: 0 4px 8px rgba(255, 255, 255, 0.1);">
			</a>
			<?php endif; ?>
					
			<div class="market active">
                <h2>Market</h2>
				<h4 style="<?php echo ($userLevel >= 8) ? 'display: none;' : ''; ?>">Her tıkladığında daha fazla puan kazanmak ister misin?</h4>
				<div>
					<button class="glow-button" id="buyLevel1" style="<?php echo ($userLevel >= 1) ? 'display: none;' : ''; ?>">Seviye 1 - 100 puan</button>
                </div>
				<div>
					<button class="glow-button" id="buyLevel2" 
						style="<?php echo ($userLevel >= 2) ? 'display: none;' : ''; ?>" 
						<?php echo ($userLevel < 1) ? 'disabled' : ''; ?>>
						Seviye 2 - 200 puan
					</button>
				</div>
				<div>
					<button class="glow-button" id="buyLevel3" 
						style="<?php echo ($userLevel >= 3) ? 'display: none;' : ''; ?>" 
						<?php echo ($userLevel < 2) ? 'disabled' : ''; ?>>
						Seviye 3 - 400 puan
					</button>
				</div>
				<div>
					<button class="glow-button" id="buyLevel4" 
						style="<?php echo ($userLevel >= 4) ? 'display: none;' : ''; ?>" 
						<?php echo ($userLevel < 3) ? 'disabled' : ''; ?>>
						Seviye 4 - 800 puan
					</button>
				</div>
				<div>
					<button class="glow-button" id="buyLevel5" 
						style="<?php echo ($userLevel >= 5) ? 'display: none;' : ''; ?>" 
						<?php echo ($userLevel < 4) ? 'disabled' : ''; ?>>
						Seviye 5 - 1600 puan
					</button>
				</div>
				<div>
					<button class="glow-button" id="buyLevel6" 
						style="<?php echo ($userLevel >= 6) ? 'display: none;' : ''; ?>" 
						<?php echo ($userLevel < 5) ? 'disabled' : ''; ?>>
						Seviye 6 - 3200 puan
					</button>
				</div>
				<div>
					<button class="glow-button" id="buyLevel7" 
						style="<?php echo ($userLevel >= 7) ? 'display: none;' : ''; ?>" 
						<?php echo ($userLevel < 6) ? 'disabled' : ''; ?>>
						Seviye 7 - 6400 puan
					</button>
				</div>
				<div>
					<button class="glow-button" id="buyLevel8" 
						style="<?php echo ($userLevel >= 8) ? 'display: none;' : ''; ?>" 
						<?php echo ($userLevel < 7) ? 'disabled' : ''; ?>>
						Seviye 8 - 12800 puan
					</button>
				</div>
				<div>
					<?php if ($userLevel >= 8): ?>
						<h4>TÜM SEVİYELERİ SATIN ALDIN :)</h4>
					<?php endif; ?>
				</div>
			</div>
			
			
			<div class="bot">
				<h2>Bot</h2>
				<p><strong>Sürekli çevrimiçi olmak zorunda kalma diye..</strong></p>
				<p><strong>BOT sizin yerinize en fazla 12 saat olmak üzere puan toplar.</strong></p>
				
				<div class="bot-level-container1">
					<?php if ($userData['bot_level'] < 1): ?>
						<div class="bot-level">
							<h3>Seviye 1</h3>
							<p>Puan Kazanç Oranı: 0.25 puan/saniye</p>
							<p>Gerekli Puan: 10,000</p>
							<?php if ($userScore >= 10000): ?>
								<button id="buyBot1" class="buy-button">Satın Al: 10,000 Puan</button>
							<?php else: ?>
								<button disabled class="insufficient-balance">BAKİYE YETERSİZ</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="bot-level-container2">
					<?php if ($userData['bot_level'] < 2): ?>
						<div class="bot-level">
							<h3>Seviye 2</h3>
							<p>Puan Kazanç Oranı: 0.55 puan/saniye</p>
							<p>Gerekli Puan: 100,000</p>
							<?php if ($userScore >= 100000): ?>
								<button id="buyBot2" class="buy-button">Satın Al: 100,000 Puan</button>
							<?php else: ?>
								<button disabled class="insufficient-balance">BAKİYE YETERSİZ</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="bot-level-container3">
					<?php if ($userData['bot_level'] < 3): ?>
						<div class="bot-level">
							<h3>Seviye 3</h3>
							<p>Puan Kazanç Oranı: 0.83 puan/saniye</p>
							<p>Gerekli Puan: 500,000</p>
							<?php if ($userScore >= 500000): ?>
								<button id="buyBot3" class="buy-button">Satın Al: 500,000 Puan</button>
							<?php else: ?>
								<button disabled class="insufficient-balance">BAKİYE YETERSİZ</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>




            <div class="friends">
				<!-- Geçiş bölümü sadece burada -->
				<div class="tab-switch">
						<button onclick="showFriends()">Referanslar</button>
						<button onclick="showLeaderboard()">Sıralama</button>
				</div>
				
			</div>
			
			<div class="friends-liste" style="display: none;">
				<h1 style="color: white; text-shadow: 2px 2px 5px rgba(0,0,0,0.5);">Referanslar</h1>
				<?php if ($ref_sayisi >= 0): ?>
					<div class="referral-link-container" style="margin-bottom: 20px;">
						<label for="referralLink">Link:</label>
						<input type="text" id="referralLink" value="https://coindanismani.com/register.php?ref=<?php echo $user_ref; ?>" readonly style="width: 80%; margin-right: 10px;">
						<button onclick="copyReferralLink()">Kopyala</button>
					</div>
				<?php endif; ?>
				<h3 style="color: white; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">REFERANS SAYINIZ: <?php echo $ref_sayisi; ?></h3>
				<div class="references">
					<?php if (count($referrals) > 0): ?>
						<ul style="list-style-type: none; padding: 0;">
							<?php foreach ($referrals as $referral): ?>
								<li style="margin-bottom: 10px;"><?php echo htmlspecialchars($referral['username']) . ' - ' . htmlspecialchars($referral['score']); ?> puan</li>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p>Henüz kimse referansınızdan kayıt olmadı.</p>
					<?php endif; ?>
				</div>
			</div>
			

			<div class="leaderboard">
				<h1 style="color: white; text-shadow: 2px 2px 5px rgba(0,0,0,0.5);">TOP 10</h1>
				<?php 
				$position = 1;
				foreach ($leaderboard as $user): 
					if ($position > 10) break;
				?>
					<p>
						<span> <?php echo $position; ?>. <?php echo $user['username']; ?></span> - <span style="color: #FFD700;"><?php echo $user['score']; ?> puan</span>
					</p>
				<?php 
					$position++;
				endforeach; 
				?>
			</div>
			<div class="tasks">
				<h2>GÖREVLER</h2>
				<?php if ($taskDavet == 0): ?>
				<div class="task">
					<img src="images/ref_icon.png" alt="Davet Logo">
					<div class="task-text">+3 REF</div>
					<button class="task-button" id="task-button" 
						<?php if ($ref_sayisi < 3) {
							echo 'disabled onclick="return false;"';
						} else {
							echo 'onclick="followDavet()"';
						} ?>>
						10,000 Puan Kazan
					</button>
				</div>
				<?php endif; ?>
				<?php if ($taskDavet == 1): ?>
				<div class="task">
					<img src="images/ref_icon.png" alt="Davet Logo">
					<div class="task-text">+10 REF</div>
					<button class="task-button" id="task-button" 
						<?php if ($ref_sayisi < 10) {
							echo 'disabled onclick="return false;"';
						} else {
							echo 'onclick="followDavet()"';
						} ?>>
						30,000 Puan Kazan
					</button>
				</div>
				<?php endif; ?>
				<?php if ($taskDavet > 1): ?>
				<div class="task">
					<img src="images/ref_icon.png" alt="Davet Logo">
					<div class="task-text">+50 REF</div>
					<button class="task-button" id="task-button" 
						<?php if ($ref_sayisi < 50) {
							echo 'disabled onclick="return false;"';
						} else {
							echo 'onclick="followDavet()"';
						} ?>>
						100,000 Puan Kazan
					</button>
				</div>
				<?php endif; ?>
				<div class="task">
					<img src="images/Instagram.png" alt="Instagram Logo">
					<div class="task-text">TAKİP ET</div>
					<button class="task-button" id="task-button" onclick="followInstagram()" <?php if ($taskInsta == 1) echo 'disabled'; ?>>1,000 Puan Kazan</button>
				</div>
				<div class="task">
					<img src="images/x.png" alt="X Logo">
					<div class="task-text">TAKİP ET</div>
					<button class="task-button" id="task-button" onclick="followTwitter()" <?php if ($taskTwitter == 1) echo 'disabled'; ?>>1,000 Puan Kazan</button>
				</div>
				<div class="task">
					<img src="images/spotify.png" alt="Spotify Logo">
					<div class="task-text">TAKİP ET</div>
					<button class="task-button" id="task-button" onclick="followSpotify()" <?php if ($taskSpotify == 1) echo 'disabled'; ?>>1,000 Puan Kazan</button>
				</div>
			</div>
			

            <div class="menu">
				<button id="menuMarket" class="active"></button>
				<button id="menuBot"></button>
				<button id="menuFriends"></button>
				<button id="menuTasks"></button>
				<button id="menuLogout" onclick="location.href='logout.php'"></button>
			</div>
			

        <?php else: ?>
				<p>Giriş için yönlendiriliyorsunuz...</p>
				<meta http-equiv="refresh" content="1;url=login.php">
		<?php endif; ?>
		
		<div id="announcement" class="announcement">
			<div class="announcement-content">
				<h2>Biraz Dinlen :)</h2>
				<p>Enerjin bitmiş gibi gözüküyor.</p>
				<p>Yenilenmesi için beklemelisin.</p>
			</div>
			<button class="buttonduyuru" id="closeButton">TAMAM</button>
		</div>
		<?php if ($earnedPoints > 0): ?>
		<div id="botMesaj" class="announcement">
			<div class="announcement-content">
				<h2>BOT Mesajı</h2>
				<p>Hoş Geldin "<?php echo $_SESSION['username']; ?>", senin için "<?php echo $earnedPoints; ?>" puan topladım.</p>
			</div>
			<button class="buttonduyuru" id="closeButton" onclick="document.getElementById('botMesaj').style.display='none'">TAMAM</button>
		</div>
		<?php endif; ?>

    </div>
	

	<div id="overlay" class="overlay"></div>
    <audio id="clickSound" src="sounds/click.mp3"></audio>
    <audio id="successSound" src="sounds/success.mp3"></audio>
    <audio id="failureSound" src="sounds/failure.mp3"></audio>
	
    <script>
		
		let energy = <?php echo $energyLimit; ?>;
        let score = <?php echo $userScore; ?>;
		let energyLevelX = <?php echo $energyLevel; ?>;
		let energyLevelY = energyLevelX + 1;
        let pointsPerClick = <?php echo $userLevel + 1; ?>;
        let tapTexts = ["TAP-TAP", "Keep going!", "Nice!", "Great!", "Awesome!", "DON'T STOP!"];
        let tapIndex = 0;
        let clickCount = 0;
		
		<?php if ($earnedPoints > 0): ?>
            showBotMesaj();
        <?php endif; ?>
		
		function updateEnergy() {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'energy_refresh.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log('Server response:', xhr.responseText);
                    const energyResponse = parseFloat(xhr.responseText);
                    if (!isNaN(energyResponse)) {
                        energy = energyResponse;  // energy değişkenini güncelle
                        updateEnergyText();
                    } else {
                        console.error('Energy value is NaN');
                    }
                } else if (xhr.readyState === 4) {
                    console.error('Error with the request. Status:', xhr.status);
                }
            };
            xhr.send('energy=' + energyLevelY);
        }

        function startEnergyUpdates() {
            if (energy < 1000) {
				updateEnergy();
				updateEnergyBar();
            } else {
                console.log('Initial energy value exceeds 1000, updates not started.');
            }
        }

        setInterval(startEnergyUpdates, 1000);
		

		
		
		function followInstagram() {
			var win = window.open("https://www.instagram.com/mrtcnygt2/", '_blank');
			
			var xhr = new XMLHttpRequest();
			xhr.open("GET", "task_instagram.php", true);
			xhr.onload = function() {
				if (xhr.status === 200) {
					if (xhr.responseText.includes("success")) {
						playSound("successSound");
						document.getElementById('task-button').disabled = true;
						setInterval(function() {
							document.write("GÖREVİ TAMAMLADINIZ");
						}, 5000);
						setInterval(function() {
							window.location.reload();
						}, 3000);
						
					} else {
						playSound("failureSound");
					}
				}
			};
			xhr.send();
		}
		
		function followTwitter() {
			var win = window.open("https://www.x.com/onlymertt/", '_blank');
			
			var xhr = new XMLHttpRequest();
			xhr.open("GET", "task_twitter.php", true);
			xhr.onload = function() {
				if (xhr.status === 200) {
					if (xhr.responseText.includes("success")) {
						playSound("successSound");
						document.getElementById('task-button').disabled = true;
						setInterval(function() {
							document.write("GÖREVİ TAMAMLADINIZ");
						}, 5000);
						setInterval(function() {
							window.location.reload();
						}, 3000);
						
					} else {
						playSound("failureSound");
					}
				}
			};
			xhr.send();
		}
		
		function followSpotify() {
			var win = window.open("https://open.spotify.com/user/31zxwu3q5kbmmj6f26oj6pzzg5tq", '_blank');
			
			var xhr = new XMLHttpRequest();
			xhr.open("GET", "task_spotify.php", true);
			xhr.onload = function() {
				if (xhr.status === 200) {
					if (xhr.responseText.includes("success")) {
						playSound("successSound");
						document.getElementById('task-button').disabled = true;
						setInterval(function() {
							document.write("GÖREVİ TAMAMLADINIZ");
						}, 5000);
						setInterval(function() {
							window.location.reload();
						}, 3000);
						
					} else {
						playSound("failureSound");
					}
				}
			};
			xhr.send();
		}
		
		function followDavet() {
			var xhr = new XMLHttpRequest();
			xhr.open("GET", "task_davet.php", true);
			xhr.onload = function() {
				if (xhr.status === 200) {
					if (xhr.responseText.includes("success")) {
						playSound("successSound");
						document.getElementById('task-button').disabled = true;
						setInterval(function() {
							document.write("GÖREVİ TAMAMLADINIZ");
						}, 1000);
						setInterval(function() {
							window.location.reload();
						}, 3000);
						
					} else {
						playSound("failureSound");
					}
				}
			};
			xhr.send();
		}
		
		
		function checkScore() {
            $.ajax({
                type: 'POST',
                url: 'check_score.php', // Puan kontrolü için bir PHP dosyası oluşturmanız gerekecek
                dataType: 'json',
                success: function(response) {
                    // Sunucudan gelen cevap başarılı ise
                    if (response.status === 'success') {
                        // Kullanıcının puanını kontrol eder ve buna göre görüntüyü günceller
                        if (response.score > 100000 && response.score < 1000000) {
                            $('#dynamicImage').attr('src', 'images/hero_passive.gif');
                        } else if (response.score > 1000000) {
                            $('#dynamicImage').attr('src', 'images/hero_passive.gif');
                        } else {
                            $('#dynamicImage').attr('src', 'images/hero_passive.gif');
                        }
                    }
                }
            });
        }

        // Belirli aralıklarla checkScore fonksiyonunu çağıran setInterval
        //$(document).ready(function() {
        //    checkScore(); // Sayfa yüklendiğinde kontrolü bir kez yapar
        //    setInterval(checkScore, 4000); // 5 saniyede bir kontrol eder (değiştirebilirsiniz)
        //});
		
		
		
		
		function showFriends() {
			document.querySelector('.friends-liste').style.display = 'block';
			document.querySelector('.leaderboard').style.display = 'none';
		}

		function showLeaderboard() {
			document.querySelector('.leaderboard').style.display = 'block';
			document.querySelector('.friends-liste').style.display = 'none';
		}
	
        function playSound(soundId) {
            let sound = document.getElementById(soundId);
            sound.play();
        }

        function updateScore() {
			var scoreElement = document.getElementById("score");
		    scoreElement.innerHTML = '<i class="fas fa-coins"></i>' + score.toLocaleString('en-US');
		    scoreElement.classList.add('breathe-animation');
			
			setTimeout(function() {
				scoreElement.classList.remove('breathe-animation');
			}, 100); // Animasyon süresi (2 saniye) ile eşleşiyor
		}
		
		function updateEnergyText() {
			document.getElementById("energy").innerHTML = '<i class="fas fa-bolt"></i>' + energy.toLocaleString('en-US');
		}
		
		function updateEnergyStyle() {
			var energyElement = document.getElementById("energy");
		    energyElement.innerHTML = '<i class="fas fa-bolt"></i>' + energy.toLocaleString('en-US');
		    energyElement.classList.add('breathe-animation');

		    // Animasyon tamamlandıktan sonra sınıfı kaldırarak tekrar oynatılmasını engelliyoruz.
		    setTimeout(function() {
			energyElement.classList.remove('breathe-animation');
		    }, 100); // Animasyon süresi (2 saniye) ile eşleşiyor
		}
		
		function updateEnergyBar() {
			// Enerji limitini alın
			var energyLimit = 1000; 
			// Enerji çubuğunun HTML öğesini seçin
			var energyBar = document.getElementById("energyBar");

			// Enerji çubuğunun genişliğini hesaplayın
			var energyWidth = (energy / energyLimit) * 100; // Enerji miktarının yüzdelik olarak genişlik hesaplaması

			// Enerji çubuğunun genişliğini güncelleyin
			energyBar.style.width = energyWidth + "%";
		}


        function updateTapText() {
            document.getElementById("tapText").innerText = tapTexts[tapIndex];
        }

        function buyLevel(level, cost) {
            if (score >= cost) {
                score -= cost;
                pointsPerClick = level + 1;
                updateScore();
                document.getElementById("buyLevel" + level).style.display = "none";
                saveScore();
                playSound("successSound");

                // Kullanıcının seviyesini güncelle
                fetch('updateLevel.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ level: level })
                })
                .then(response => response.json())
                .then(data => console.log(data));
				setInterval(function() {
							window.location.reload();
						}, 2000);
            } else {
                playSound("failureSound");
            }
        }

        function saveScore() {
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ score: score })
            })
            .then(response => response.json())
            .then(data => console.log(data));
        }
		
		
        document.querySelector(".circle").addEventListener("pointerdown", function(event) {
			if (navigator.vibrate) {
				navigator.vibrate(200); // 200 milisaniye titreşim
			}

			this.classList.add("clicked");
			setTimeout(() => {
				this.classList.remove("clicked");
			}, 150);

			if (energy <= pointsPerClick) {
				showAnnouncement();
				return;
			} else {
				// Tıklama sayısını belirle
				let clicks = 1; // Default olarak 1 tıklama say
				if (event.pointerType === "mouse") {
					clicks = 1; // Mouse click
				} else if (event.pointerType === "touch") {
					clicks = event.detail || 1; // Touch click
				}

				// Tıklama sayısını puan ile çarparak skora ekle
				let pointsGained = clicks * pointsPerClick;
				score += pointsGained;

				// Skoru güncelle
				updateScore();

				// Tıklama sayısını güncelle
				clickCount += clicks;

				// Her 10 tıklamada bir tapText güncelle
				if (clickCount % 10 === 0) {
					tapIndex = (tapIndex + 1) % tapTexts.length;
					updateTapText();
				}

				// Sesi çal ve skoru kaydet
				playSound("clickSound");
				saveScore();

				energy -= pointsGained;

				fetch('energy_hesap.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({ energy: energy }) // JSON verisine "energy" anahtarını ekleyin
				})

				// İlgili resmi değiştir
				$('#dynamicImage').attr('src', 'images/T_click.png');

				updateEnergyStyle();
				updateEnergyText();
				updateEnergyBar();

				// Resmi eski haline geri döndürmek için setTimeout kullanarak işlemi gerçekleştirin
				setTimeout(() => {
					$('#dynamicImage').attr('src', 'images/T.png');
				}, 500); // Örneğin, 200 milisaniye sonra eski haline geri dönsün
			}
		});
		

        document.getElementById("buyLevel1").addEventListener("click", function() { buyLevel(1, 100); });
        document.getElementById("buyLevel2").addEventListener("click", function() { buyLevel(2, 200); });
        document.getElementById("buyLevel3").addEventListener("click", function() { buyLevel(3, 400); });
        document.getElementById("buyLevel4").addEventListener("click", function() { buyLevel(4, 800); });
        document.getElementById("buyLevel5").addEventListener("click", function() { buyLevel(5, 1600); });
        document.getElementById("buyLevel6").addEventListener("click", function() { buyLevel(6, 3200); });
        document.getElementById("buyLevel7").addEventListener("click", function() { buyLevel(7, 6400); });
		document.getElementById("buyLevel8").addEventListener("click", function() { buyLevel(8, 12800); });

        document.getElementById("menuMarket").addEventListener("click", function() {
            document.querySelector(".market").classList.add("active");
            document.querySelector(".bot").classList.remove("active");
            document.querySelector(".friends").classList.remove("active");
			document.querySelector(".tasks").classList.remove("active");
            document.getElementById("menuMarket").classList.add("active");
            document.getElementById("menuBot").classList.remove("active");
            document.getElementById("menuFriends").classList.remove("active");
			document.getElementById("menuTasks").classList.remove("active");
			document.querySelector('.leaderboard').style.display = 'none';
			document.querySelector('.friends-liste').style.display = 'none';
        });

        document.getElementById("menuBot").addEventListener("click", function() {
            document.querySelector(".market").classList.remove("active");
            document.querySelector(".bot").classList.add("active");
            document.querySelector(".friends").classList.remove("active");
			document.querySelector(".tasks").classList.remove("active");
            document.getElementById("menuMarket").classList.remove("active");
            document.getElementById("menuBot").classList.add("active");
            document.getElementById("menuFriends").classList.remove("active");
			document.getElementById("menuTasks").classList.remove("active");
			document.querySelector('.leaderboard').style.display = 'none';
			document.querySelector('.friends-liste').style.display = 'none';
        });

        document.getElementById("menuFriends").addEventListener("click", function() {
            document.querySelector(".market").classList.remove("active");
            document.querySelector(".bot").classList.remove("active");
			document.querySelector(".tasks").classList.remove("active");
            document.querySelector(".friends").classList.add("active");
            document.getElementById("menuMarket").classList.remove("active");
            document.getElementById("menuBot").classList.remove("active");
			document.getElementById("menuTasks").classList.remove("active");
            document.getElementById("menuFriends").classList.add("active");
			document.querySelector('.friends-liste').style.display = 'block';
			document.querySelector('.leaderboard').style.display = 'none';
        });
		
		document.getElementById("menuTasks").addEventListener("click", function() {
            document.querySelector(".market").classList.remove("active");
            document.querySelector(".bot").classList.remove("active");
			document.querySelector(".tasks").classList.add("active");
            document.querySelector(".friends").classList.remove("active");
            document.getElementById("menuMarket").classList.remove("active");
            document.getElementById("menuBot").classList.remove("active");
			document.getElementById("menuTasks").classList.add("active");
            document.getElementById("menuFriends").classList.remove("active");
			document.querySelector('.leaderboard').style.display = 'none';
			document.querySelector('.friends-liste').style.display = 'none';
        });
		function copyReferralLink() {
			var copyText = document.getElementById("referralLink");
			copyText.select();
			copyText.setSelectionRange(0, 99999);
			document.execCommand("copy");
			alert("Merhaba, TAP-TAP oyununa referansım ile kayıt olabilirsin:  " + copyText.value);
		}
		document.addEventListener('DOMContentLoaded', (event) => {
			const botButtons = document.querySelectorAll('.buy-button');
			botButtons.forEach(button => {
				button.addEventListener('click', () => {
					const level = button.id.replace('buyBot', '');
					const cost = getBotCost(level);
					buyBot(level, cost);
				});
			});
		});
		
		function showBotMesaj() {
			botMesaj.style.display = "block";
			overlay.style.display = "block";
		}
		
		function showAnnouncement() {
			announcement.style.display = "block";
			overlay.style.display = "block";
		}
		
		document.addEventListener("DOMContentLoaded", function() {
			var closeButton = document.getElementById("closeButton");
			var announcement = document.getElementById("announcement");
			var botMesaj = document.getElementById("botMesaj");
			var overlay = document.getElementById("overlay");
			
			closeButton.addEventListener("click", function() {
				botMesaj.style.display = "none";
				announcement.style.display = "none";
				overlay.style.display = "none";
			});

			overlay.addEventListener("click", function() {
				botMesaj.style.display = "none";
				announcement.style.display = "none";
				overlay.style.display = "none";
			});
		});

		function getBotCost(level) {
			switch (level) {
				case '1':
					return 10000;
				case '2':
					return 100000;
				case '3':
					return 500000;
				default:
					return 0;
			}
		}

		function buyBot(level, cost) {
			if (score >= cost) {
				score -= cost;
				updateScore();
				document.getElementById("buyBot" + level).style.display = "none";
				saveScore();
				
				
				// Kullanıcının bot seviyesini güncelle
				fetch('updateBotLevel.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({ bot_level: level })
				})
				
				.then(response => response.json())
				.then(data => console.log(data));
				setInterval(function() {
							window.location.reload();
						}, 2000);
			} else {
				playSound("failureSound");
			}
		}

		document.getElementById("buyBot1").addEventListener("click", function() { buyBot(1, 10000); });
		document.getElementById("buyBot2").addEventListener("click", function() { buyBot(2, 100000); });
		document.getElementById("buyBot3").addEventListener("click", function() { buyBot(3, 500000); });
		
		// Sayfa yüklendiğinde veya herhangi bir işlem gerçekleştiğinde kullanıcının son giriş zamanını güncelle
		function updateLastLogin() {
			fetch('updateLastLogin.php')
			.then(response => response.json())
			.then(data => console.log(data))
			.catch(error => console.error('Error updating last login:', error));
		}
		updateLastLogin();


		
    </script>
</body>
</html>