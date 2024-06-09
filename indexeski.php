<?php
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
    $stmt = $conn->prepare("SELECT user_ref, score, level, ref_sayisi, bot_level, last_login, task_insta FROM users WHERE username = :username");
    $stmt->bindParam(':username', $user);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    $userScore = $userData['score'];
    $userLevel = $userData['level'];
    $ref_sayisi = $userData['ref_sayisi'];
    $user_ref = $userData['user_ref'];
	$lastLogin = $userData['last_login'];
	$botLevel = isset($userData['bot_level']) ? intval($userData['bot_level']) : 0;
	$taskInsta = $userData['task_insta'];
	
	
	echo "Last Login: " . $lastLogin;
	error_reporting(E_ALL);
	
	


if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    $currentTime = date('Y-m-d H:i:s'); // Şu anki tarihi ve saati al
    // Kullanıcının son giriş zamanını güncelle
    $stmt = $conn->prepare("UPDATE users SET last_login = :last_loginn WHERE username = :username");
    $stmt->bindParam(':last_loginn', $currentTime);
    $stmt->bindParam(':username', $user);
    $success = $stmt->execute(); // Sorguyu çalıştır ve başarılı olup olmadığını kontrol et
    if (!$success) {
        // SQL sorgusu başarısız olduysa hata mesajını göster ve işlemi durdur
        die("SQL hatası: " . $stmt->errorInfo()[2]);
    }
}
	
	
	function calculatePoints($botLevel, $lastLogin) {
		date_default_timezone_set('Europe/Istanbul'); // Türkiye saatine göre ayarla
		$currentTimex = time();
		$lastLoginTime = strtotime($lastLogin);
		$timeDifference = $currentTimex - $lastLoginTime;
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
	
	





		
	// Kullanıcının kazandığı puanı hesaplayın
    $earnedPoints = calculatePoints($botLevel, $lastLogin);
	
	// Mevcut puanı güncelle
	$userScore += $earnedPoints;
	
	// Veritabanında puanı güncelle
	$stmt = $conn->prepare("UPDATE users SET score = score + :earnedPoints WHERE username = :username");
	$stmt->bindParam(':earnedPoints', $earnedPoints);
	$stmt->bindParam(':username', $user);
	$stmt->execute();
	

	// Puan bildirimini ekrana yazdırın
    if ($earnedPoints >= 0) {
        echo "<script>alert('Şu kadar kazandınız: $earnedPoints puan!');</script>";
    }
	

    // Kullanıcının referans kodundan gelen kişileri alalım
    $stmt = $conn->prepare("SELECT u.username, u.score FROM users u JOIN users r ON u.where_ref = r.user_ref WHERE r.username = :username");
    $stmt->bindParam(':username', $user);
    $stmt->execute();
    $referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
// Global sıralama için veritabanından kullanıcı adlarını ve puanlarını çek
$stmt = $conn->prepare("SELECT username, score FROM users ORDER BY score DESC");
$stmt->execute();
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
$bot_level = isset($userData['bot_level']) ? $userData['bot_level'] : 0;

include 'updatelastlogin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAP-TAP Game</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<style>
	html {
		scroll-behavior: smooth;
		height: 100%;
		margin: 0;
		padding: 0;
	}

    body {
        background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
        color: #e0e0e0;
        font-family: 'Roboto', sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        height: 100vh;
        margin: 0;
        overflow: hidden;
		padding-top: 20px;
    }

    .bot-level-container1, .bot-level-container2, .bot-level-container3 {
        border: 2px solid #34495e;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: rgba(44, 62, 80, 0.7);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .game-container {
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        margin-top: 20px;
        overflow-y: auto;
        max-height: calc(100vh - 150px);
    }

    .info {
        margin: 20px;
        background: rgba(52, 73, 94, 0.7);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    }

    .circle {
        display: inline-block;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background-color: #16a085;
        position: relative;
        box-shadow: 0 0 15px rgba(22, 160, 133, 0.7);
        animation: breathe 1s infinite alternate;
        transition: transform 0.3s;
    }

    .circle.clicked {
        animation: shake 0.5s;
    }

    .circle-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    @keyframes breathe {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .circle img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    button {
        background-color: #1abc9c;
        color: #fff;
        border: none;
        padding: 10px 20px;
        margin: 10px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    button:hover {
        background-color: #16a085;
    }

    .button-container {
        display: flex;
        justify-content: center;
    }

    #score {
        font-size: 2em;
        font-weight: bold;
        color: #f39c12;
        text-shadow: 0 0 10px #f39c12, 0 0 20px #f39c12, 0 0 30px #f39c12;
    }

    #tapText {
        font-size: 1.5em;
        font-weight: bold;
        color: #f39c12;
        text-shadow: 0 0 10px #f39c12, 0 0 20px #f39c12, 0 0 30px #f39c12;
    }

    .market, .bot, .friends, .tasks {
        display: none;
        margin: 20px;
    }

    .market.active, .bot.active, .friends.active, .tasks.active {
        display: block;
        background: rgba(52, 73, 94, 0.7);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
		margin: 10px;
    }

    .menu {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-around;
        background-color: #0f2027;
        z-index: 10;
        box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.5);
    }

    .menu button {
        flex-grow: 1;
        padding: 15px;
        color: #fff;
        background-color: #34495e;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
        border-top: 1px solid #1abc9c;
    }

    .menu button.active, .menu button:hover {
        background-color: #1abc9c;
    }

    .friends-list {
        display: flex;
        justify-content: space-between;
    }

    .friends-list h3 {
        font-size: 1.5em;
        font-weight: bold;
    }

    .friends-liste, .leaderboard {
        flex-grow: 1;
        margin: 10px;
        padding: 10px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .friends-list .top-users {
        font-size: 1.2em;
        font-weight: bold;
        color: gold;
    }

    .level-bar {
        width: 20%;
        background-color: #ddd;
        border-radius: 5px;
        margin: 10px auto;
        height: 20px;
        position: relative;
        overflow: hidden;
    }

    .level-progress {
        height: 100%;
        background-color: #16a085;
        width: 0;
        transition: width 0.5s;
        border-radius: 5px;
    }

    .level-text {
        text-align: center;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .login-btn, .register-btn {
        padding: 10px 20px;
        margin: 0 10px;
        font-size: 16px;
        color: white;
        background-color: #1abc9c;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .login-btn:hover, .register-btn:hover {
        background-color: #16a085;
    }

    .circle-container-no {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .circle-no {
        width: 200px;
        height: 200px;
        background-color: black;
        border-radius: 50%;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 0 20px 5px rgba(255, 0, 0, 0.8);
        overflow: hidden;
    }

    .circle-no img-no {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }

    .button-container-no {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .button-container-no button-no {
        background-color: #ff0000;
        color: #fff;
        border: none;
        padding: 15px 30px;
        margin: 0 10px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .button-container-no button-no:hover {
        background-color: #e60000;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
    }

    .button-container-no button-no:active {
        transform: translateY(2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
    }
	
	.task {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }
        .task img {
            width: 50px;
            height: 50px;
        }
        .task-text {
            flex-grow: 1;
            margin-left: 10px;
        }
        .task-button {
            background-color: #3897f0;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .task-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
</style>
</head>
<body>
    <div class="game-container">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="info">
				<p>Merhaba <?php echo htmlspecialchars($_SESSION['username']); ?>! Oyunda hedefinize ulaşmak için ekrandaki daireye tıklayın ve puanları toplayın. Market'ten seviyeleri satın alarak puan kazanma oranını arttırabilirsiniz.</p>
				<p> <span id="score" data-change="false"><?php echo number_format($userScore, 2); ?></span></p>
			</div>

            <div class="circle-container">
                <div class="circle" style="background-color: black;">
                    <img src="ezgif.gif" alt="Description of your image" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
            </div>
			
            <span id="tapText">TAP-TAP</span>
			<?php
			// Yüzdeyi hesapla ve formatla
			$percent = number_format(($userLevel / 7) * 100, 2);
			?>
			<div class="level-bar">
				<div class="level-progress" style="width: <?php echo ($userLevel / 7) * 100; ?>%;"></div>
			</div>
			<div class="level-text">Seviye: <?php echo $userLevel; ?> (%<?php echo $percent; ?>)</div>			
            
			
			<div class="market active">
                <h2>Market</h2>
                <button id="buyLevel1" style="<?php echo ($userLevel >= 1) ? 'display: none;' : ''; ?>">Multitap: Seviye 1 - 100 puan</button>
                <button id="buyLevel2" style="<?php echo ($userLevel >= 2) ? 'display: none;' : ''; ?>">Multitap: Seviye 2 - 200 puan</button>
                <button id="buyLevel3" style="<?php echo ($userLevel >= 3) ? 'display: none;' : ''; ?>">Multitap: Seviye 3 - 400 puan</button>
                <button id="buyLevel4" style="<?php echo ($userLevel >= 4) ? 'display: none;' : ''; ?>">Multitap: Seviye 4 - 800 puan</button>
                <button id="buyLevel5" style="<?php echo ($userLevel >= 5) ? 'display: none;' : ''; ?>">Multitap: Seviye 5 - 1600 puan</button>
                <button id="buyLevel6" style="<?php echo ($userLevel >= 6) ? 'display: none;' : ''; ?>">Multitap: Seviye 6 - 3200 puan</button>
                <button id="buyLevel7" style="<?php echo ($userLevel >= 7) ? 'display: none;' : ''; ?>">Multitap: Seviye 7 - 6400 puan</button>
				<?php if ($userLevel >= 7): ?>
					<p>TÜM SEVİYELERİ SATIN ALDINIZ - BİR SONRAKİ GÜNCELLEMEYİ BEKLEMELİSİN :)</p>
				<?php endif; ?>
			</div>
			
			
			<div class="bot">
				<h2>Bot</h2>
				<p><strong>Unutmayın! BOT sizin yerinize en fazla 12 saat çalışabilir.</strong></p>
				
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
				<h1 style="color: gold; text-shadow: 2px 2px 5px rgba(0,0,0,0.5);">Referanslar</h1>
				<?php if ($ref_sayisi >= 0): ?>
					<p>Link: <input type="text" id="referralLink" value="https://coindanismani.com/register.php?ref=<?php echo $user_ref; ?>" readonly> <button onclick="copyReferralLink()">Kopyala</button></p>
				<?php endif; ?>
				<h3 style="color: light-gray; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">REFERANS SAYINIZ: <?php echo $ref_sayisi; ?></h3>
				<div class="references" >
					<?php if (count($referrals) > 0): ?>
						<?php foreach ($referrals as $referral): ?>
							<p><?php echo $referral['username']; ?> - <?php echo $referral['score']; ?> puan</p>
						<?php endforeach; ?>
					<?php else: ?>
						<p>Henüz kimse referansınızdan kayıt olmadı.</p>
					<?php endif; ?>
				</div>
			</div>
			

			<div class="leaderboard" style="display: none;">
				<h1 style="color: gold; text-shadow: 2px 2px 5px rgba(0,0,0,0.5);">Global Sıralama</h1>
				<?php $position = 1; ?>
				<?php foreach ($leaderboard as $user): ?>
					<p
						<span> <?php echo $position; ?>. <?php echo $user['username']; ?></span> - <span style="color: #FFD700;"><?php echo $user['score']; ?> puan</span>
					</p>
					<?php $position++; ?>
				<?php endforeach; ?>
				
			</div>
			
			<<div class="tasks">
				<h2>GÖREVLER</h2>
				<div class="task">
					<img src="Instagram.png" alt="Instagram Logo">
					<div class="task-text">INSTAGRAM TAKİP ET</div>
					<button class="task-button" id="task-button" onclick="followInstagram()" <?php if ($taskInsta == 1) echo 'disabled'; ?>>1000 Puan</button>
				</div>
			</div>

            <div class="menu">
                <button id="menuMarket" class="active">Shop</button>
                <button id="menuBot">Bot</button>
                <button id="menuFriends">Friends</button>
				<button id="menuTasks">Tasks</button>
                <button onclick="location.href='logout.php'">Quit</button>
            </div>
        <?php else: ?>
            <div class="circle-container-no">
				<div class="circle-no">
					<img src="ezgif.gif" alt="Description of your image" style="max-width: 100%; max-height: 100%; object-fit: contain;">
				</div>
				<div class="button-container-no">
					<button class="login-btn" onclick="location.href='login.php'">Giriş Yap</button>
					<button class="register-btn" onclick="location.href='register.php'">Kayıt Ol</button>
				</div>
			</div>
           
        <?php endif; ?>
    </div>
    <audio id="clickSound" src="click.mp3"></audio>
    <audio id="successSound" src="success.mp3"></audio>
    <audio id="failureSound" src="failure.mp3"></audio>

    <script>
        let score = <?php echo $userScore; ?>;
        let pointsPerClick = <?php echo $userLevel + 1; ?>;
        let tapTexts = ["TAP-TAP", "Keep going!", "Nice!", "Great!", "Awesome!"];
        let tapIndex = 0;
        let clickCount = 0;
		
		
		function followInstagram() {
			var win = window.open("https://www.instagram.com/mrtcnygt2/", '_blank');
			
			var xhr = new XMLHttpRequest();
			xhr.open("GET", "task_update.php", true);
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
			document.getElementById("score").innerText = score.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
		});

        document.getElementById("buyLevel1").addEventListener("click", function() { buyLevel(1, 100); });
        document.getElementById("buyLevel2").addEventListener("click", function() { buyLevel(2, 200); });
        document.getElementById("buyLevel3").addEventListener("click", function() { buyLevel(3, 400); });
        document.getElementById("buyLevel4").addEventListener("click", function() { buyLevel(4, 800); });
        document.getElementById("buyLevel5").addEventListener("click", function() { buyLevel(5, 1600); });
        document.getElementById("buyLevel6").addEventListener("click", function() { buyLevel(6, 3200); });
        document.getElementById("buyLevel7").addEventListener("click", function() { buyLevel(7, 6400); });

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
        alert("Merhaba, TAP-TAP oyununa bu referans ile kayıt olabilirsin:  " + copyText.value);
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