<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

session_start();
include 'db_connect.php';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Güvenlik kontrolleri
    $user = htmlspecialchars($user);
    $pass = htmlspecialchars($pass);

    // SHA-1 ile şifreyi kontrol et
    $pass_hash = sha1($pass);

    // Kullanıcı adı için SQL enjeksiyon koruması
    $user = $conn->real_escape_string($user);

    $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass_hash'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $user;
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Yanlış kullanıcı adı veya şifre";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tap-Tap Kullanıcı Girişi</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

		* {
		  margin: 0;
		  padding: 0;
		  box-sizing: border-box;
		  font-family: 'poppins',sans-serif;
		}

		body {
		  display: flex;
		  align-items: center;
		  justify-content: center;
		  min-height: 100vh;
		  background-image: url(https://blackwallpaperhd.com/wp-content/uploads/2023/02/yellow1-576x1024.jpg);
		  background-repeat: no-repeat;
		  background-position: center;
		  background-size: cover;
		}

		section {
			position: relative;
			max-width: 400px;
			background-color: transparent;
			border: 2px solid rgba(255, 255, 255, 0.5);
			border-radius: 20px;
			backdrop-filter: blur(55px);
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 2rem 3rem;
		}

		h1 {
			font-size: 2rem;
			color: #fff;
			text-align: center;
		}

		.inputbox {
			position: relative;
			margin: 30px 0;
			max-width: 310px;
			border-bottom: 2px solid #fff;
		}

		.inputbox label {
			position: absolute;
			top: 50%;
			left: 5px;
			transform: translateY(-50%);
			color: #fff;
			font-size: 1rem;
			pointer-events: none;
			transition: all 0.5s ease-in-out;
		}

		input:focus ~ label, 
		input:valid ~ label {
			top: -5px;
		}

		.inputbox input {
			width: 100%;
			height: 60px;
			background: transparent;
			border: none;
			outline: none;
			font-size: 1rem;
			padding: 0 35px 0 5px;
			color: #fff;
		}

		.inputbox ion-icon {
			position: absolute;
			right: 8px;
			color: #fff;
			font-size: 1.2rem;
			top: 20px;
		}

		.forget {
			margin: 35px 0;
			font-size: 0.85rem;
			color: #fff;
			display: flex;
			justify-content: space-between;
		 
		}

		.forget label {
			display: flex;
			align-items: center;
		}

		.forget label input {
			margin-right: 3px;
		}

		.forget a {
			color: #fff;
			text-decoration: none;
			font-weight: 600;
		}

		.forget a:hover {
			text-decoration: underline;
		}

		button {
			width: 100%;
			height: 40px;
			border-radius: 40px;
			background-color: rgb(255, 255,255, 1);
			border: none;
			outline: none;
			cursor: pointer;
			font-size: 1rem;
			font-weight: 600;
			transition: all 0.4s ease;
		}

		button:hover {
		  background-color: rgb(255, 255,255, 0.5);
		}

		.register {
			font-size: 0.9rem;
			color: #fff;
			text-align: center;
			margin: 25px 0 10px;
		}

		.register p a {
			text-decoration: none;
			color: #fff;
			font-weight: 600;
		}

		.register p a:hover {
			text-decoration: underline;
		}
    </style>
</head>
<body>
    <section>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1>Kullanıcı Girişi</h1>
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $result->num_rows == 0) { ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php } ?>
            <div class="inputbox">
                <ion-icon name="mail-outline"></ion-icon>
                <input type="text" name="username" required>
                <label for="">Kullanıcı Adı</label>
            </div>
            <div class="inputbox">
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" name="password" required>
                <label for="">Şifre</label>
            </div>
            <button type="submit">Giriş yap</button>
            <div class="register">
                <p>Hesabın yok mu? <a href="register.php">Kayıt Ol</a></p>
            </div>
        </form>
    </section>
</body>
</html>
