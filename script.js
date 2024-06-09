let score = <?php echo $userScore; ?>;
let pointsPerClick = <?php echo $userLevel + 1; ?>;
let tapTexts = ["TAP-TAP", "Keep going!", "Nice!", "Great!", "Awesome!", "DON'T STOP!"];
let tapIndex = 0;
let clickCount = 0;


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
					$('#dynamicImage').attr('src', 'ezgif2.gif');
				} else if (response.score > 1000000) {
					$('#dynamicImage').attr('src', 'ezgif3.gif');
				} else {
					$('#dynamicImage').attr('src', 'ezgif.gif');
				}
			}
		}
	});
}

// Belirli aralıklarla checkScore fonksiyonunu çağıran setInterval
$(document).ready(function() {
	checkScore(); // Sayfa yüklendiğinde kontrolü bir kez yapar
	setInterval(checkScore, 4000); // 5 saniyede bir kontrol eder (değiştirebilirsiniz)
});






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



