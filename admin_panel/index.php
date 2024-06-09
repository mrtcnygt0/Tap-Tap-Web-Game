<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
include('db_connect.php');

// Sayfa başlıklarını dinamik hale getirin
function getPageTitle($page) {
    switch ($page) {
        case 'dashboard':
            return 'Genel Bakış';
        case 'users':
            return 'Kullanıcılar';
        case 'top10':
            return 'Top 10';
        case 'profile':
            return 'Profil';
        default:
            return 'Admin Paneli';
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$pageTitle = getPageTitle($page);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Admin Paneli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <nav>
            <ul>
                <li><a href="index.php?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">Genel Bakış</a></li>
                <li><a href="index.php?page=users" class="<?php echo $page == 'users' ? 'active' : ''; ?>">Kullanıcılar</a></li>
                <li><a href="index.php?page=top10" class="<?php echo $page == 'top10' ? 'active' : ''; ?>">Top 10</a></li>
                <li><a href="index.php?page=profile" class="<?php echo $page == 'profile' ? 'active' : ''; ?>">Profil</a></li>
                <li><a href="logout.php">Çıkış Yap</a></li>
            </ul>
            <button id="theme-toggle">Temayı Değiştir</button>
        </nav>
        <main class="content">
            <?php
                $pagePath = "pages/$page.php";
                if (file_exists($pagePath)) {
                    include($pagePath);
                } else {
                    echo '<h1>Sayfa Bulunamadı</h1>';
                }
            ?>
        </main>
    </div>
    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tüm bağlantıları al
            const links = document.querySelectorAll('nav ul li a');
            // Aktif bağlantıya 'active' sınıfı ekle
            links.forEach(link => {
                if (link.href === window.location.href) {
                    link.classList.add('active');
                }
            });

            // Tema değiştirici buton
            const themeToggle = document.getElementById('theme-toggle');
            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('dark-theme');
                localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
            });

            // Sayfa yüklendiğinde tema kontrolü
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-theme');
            }
        });

        // Sayfa yüklendiğinde hoş geldiniz mesajı
        window.onload = () => {
            const welcomeMessage = document.createElement('div');
            welcomeMessage.className = 'welcome-message';
            welcomeMessage.innerText = 'Hoş geldiniz, Admin!';
            document.body.appendChild(welcomeMessage);
            setTimeout(() => {
                welcomeMessage.classList.add('visible');
            }, 500);
            setTimeout(() => {
                welcomeMessage.classList.remove('visible');
                setTimeout(() => {
                    welcomeMessage.remove();
                }, 500);
            }, 4000);
        };
    </script>
</body>
</html>
