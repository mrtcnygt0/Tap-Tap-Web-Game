<?php
$admin_id = $_SESSION['admin_id']; // Admin girişinde oturum açma ile bu id alınır

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    $updateQuery = "UPDATE admin SET username='$username', password='$password' WHERE id='$admin_id'";
    if ($conn->query($updateQuery) === TRUE) {
        echo "Profil güncellendi.";
    } else {
        echo "Hata: " . $conn->error;
    }
}

$adminQuery = "SELECT username, password FROM admin WHERE id='$admin_id'";
$adminResult = $conn->query($adminQuery);
$admin = $adminResult->fetch_assoc();
?>

<h2>Profil</h2>
<form method="POST">
    <label for="username">Kullanıcı Adı:</label>
    <input type="text" id="username" name="username" value="<?php echo $admin['username']; ?>" required><br><br>
    <label for="password">Şifre:</label>
    <input type="password" id="password" name="password" value="<?php echo $admin['password']; ?>" required><br><br>
    <input type="submit" value="Güncelle">
</form>
