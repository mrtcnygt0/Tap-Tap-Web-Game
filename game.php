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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Oyun</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
        
        body {
            background: url('images/game_background.gif') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            overflow: hidden;
            font-family: 'Press Start 2P', cursive;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        #gameArea {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .icon, .badIcon {
            position: absolute;
            width: 50px;
            height: 50px;
            background-size: cover;
            background-position: center;
            border-radius: 50%;
            transition: transform 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: iconFall 3s linear infinite;
        }
        .icon {
            background-image: url('images/game_icon2.png');
            animation: rotateIcon 3s linear infinite;
        }
        .badIcon {
            background-image: url('images/game_bomb.gif');
            border: 2px solid #fff;
        }
        .explosion {
            position: absolute;
            width: 68px;
            height: 68px;
            background-size: cover;
            background-position: center;
            border-radius: 50%;
            display: none;
            z-index: 999;
            background-image: url('images/game_patlama.gif');
        }
        .icon:hover, .badIcon:hover {
            transform: scale(1.2);
        }
        #score, #gameDuration {
            position: absolute;
            color: #fff;
            font-size: 24px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 5px;
            z-index: 10;
            border: 2px solid #fff;
        }
        #score {
            top: 20px;
            right: 20px;
        }
        #gameDuration {
            top: 20px;
            left: 20px;
        }
        #startButton {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px 40px;
            font-size: 24px;
            z-index: 100;
            background-color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 50px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: background-color 0.3s, transform 0.3s;
        }
        #startButton:hover {
            background-color: #f0f0f0;
            transform: scale(1.05);
        }
        @media (max-width: 768px) {
            #score, #gameDuration {
                font-size: 20px;
                padding: 8px;
            }
            #startButton {
                padding: 15px 30px;
                font-size: 20px;
            }
        }
        @media (max-width: 480px) {
            #score, #gameDuration {
                font-size: 16px;
                padding: 5px;
            }
            #startButton {
                padding: 10px 20px;
                font-size: 16px;
            }
        }
        @keyframes iconFall {
            from {
                top: -50px;
            }
            to {
                top: calc(100vh - 50px);
            }
        }
        @keyframes rotateIcon {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        .neon {
            color: #fff;
            text-shadow: 
                0 0 5px #fff,
                0 0 10px #fff,
                0 0 15px #fff,
                0 0 20px #ff00ff,
                0 0 25px #ff00ff,
                0 0 30px #ff00ff,
                0 0 35px #ff00ff;
        }
    </style>
</head>
<body>
    <div id="gameArea">
        <div id="score" class="neon">Puan: 0</div>
        <div id="gameDuration" class="neon">Süre: 20</div>
        <button id="startButton">Başla</button>
        <div id="explosionEffect" class="explosion"></div>
        <audio id="clickSound" src="sounds/collectPoints.mp3"></audio>
        <audio id="readySound" src="sounds/ready.mp3"></audio>
        <audio id="startSound" src="sounds/countdown.mp3"></audio>
    </div>

    <script>
        let score = 0;
        let gameInterval;
        let durationInterval;
        let gameDuration = 26000; // 26 saniye
        let remainingTime = 20; // Süreyi saniye olarak başlatıyoruz

        document.getElementById('readySound').play();

        document.getElementById('startButton').addEventListener('click', startGame);

        function startGame() {
            document.getElementById('startButton').style.display = 'none';
            gameInterval = setInterval(createIcon, 500); // İkon oluşturma sıklığını azalttık
            durationInterval = setInterval(updateDuration, 1000); // Her saniye updateDuration fonksiyonunu çalıştır
            setTimeout(endGame, gameDuration);
            document.getElementById('startSound').play();
        }

        function createIcon() {
            const gameArea = document.getElementById('gameArea');
            const icon = document.createElement('div');
            icon.className = Math.random() < 0.8 ? 'icon' : 'badIcon'; // %80 ihtimalle iyi ikon, %20 kötü ikon
            icon.style.top = '0px';
            const maxWidth = window.innerWidth - 50; // 50px icon width
            icon.style.left = Math.random() * maxWidth + 'px';

            if (icon.className === 'icon') {
                icon.addEventListener('click', () => {
                    score += 100;
                    document.getElementById('score').innerText = 'Puan: ' + score;
                    document.getElementById('clickSound').play();
                    icon.remove();
                });
            } else {
                icon.addEventListener('click', (event) => {
                    score -= 1000;
                    document.getElementById('score').innerText = 'Puan: ' + score;
                    showExplosion(event.clientX, event.clientY);
                    icon.remove();
                });
            }

            gameArea.appendChild(icon);

            let iconFallInterval = setInterval(() => {
                let iconTop = parseInt(icon.style.top);
                if (iconTop < window.innerHeight - 50) { // İkonun tamamen görünene kadar düşmesi için - 50 yaptık
                    icon.style.top = iconTop + 10 + 'px'; // Hızı artırmak için 10px
                } else {
                    clearInterval(iconFallInterval);
                    icon.remove();
                }
            }, 30); // Düşme hızını artırmak için intervali düşürdük
        }

        function showExplosion(x, y) {
            const explosion = document.getElementById('explosionEffect');
            explosion.style.left = (x - 34) + 'px'; // 68px / 2 = 34px (merkezlemek için)
            explosion.style.top = (y - 34) + 'px';
            explosion.style.display = 'block';
            setTimeout(() => {
                explosion.style.display = 'none';
            }, 500); // 0.5 saniye sonra patlamayı gizle
        }

        function updateDuration() {
            if (remainingTime > 5) {
                remainingTime--;
            } else if (remainingTime > 0) {
                remainingTime -= 0.5; // 2 saniyede 1 azalması için
                document.getElementById('gameDuration').style.color = 'red';
            }
            document.getElementById('gameDuration').innerText = 'Süre: ' + Math.ceil(remainingTime);
        }

        function endGame() {
            clearInterval(gameInterval);
            clearInterval(durationInterval);
            alert('Oyun bitti! Toplam Puan: ' + score);
            updateScore();
        }

        function updateScore() {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'game_score.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    window.location.href = 'index.php';
                }
            };
            xhr.send('score=' + score);
        }

        window.addEventListener('resize', () => {
            const icons = document.querySelectorAll('.icon, .badIcon');
            icons.forEach(icon => {
                const maxWidth = window.innerWidth - 50; // 50px icon width
                let left = parseInt(icon.style.left);
                if (left > maxWidth) {
                    icon.style.left = maxWidth + 'px';
                }
            });
        });
    </script>
</body>
</html>
