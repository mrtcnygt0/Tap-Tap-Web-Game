document.addEventListener('DOMContentLoaded', () => {
    // Tüm bağlantıları al
    const links = document.querySelectorAll('nav ul li a');
    
    // Aktif bağlantıya 'active' sınıfı ekle
    links.forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });

    // Animasyon için tüm linklere event listener ekleyin
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelector('.content').classList.add('fade-out');
            setTimeout(() => {
                window.location.href = link.href;
            }, 500); // Fade-out süresi
        });
    });

    // Kullanıcı giriş mesajı
    showWelcomeMessage();

    // Temalar arası geçiş için event listener
    const themeToggle = document.querySelector('#theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
});

// Hoşgeldiniz mesajı
function showWelcomeMessage() {
    const message = document.createElement('div');
    message.textContent = 'Admin Paneline Hoşgeldiniz!';
    message.className = 'welcome-message';
    document.body.appendChild(message);

    setTimeout(() => {
        message.classList.add('visible');
    }, 100);

    setTimeout(() => {
        message.classList.remove('visible');
        setTimeout(() => {
            message.remove();
        }, 500);
    }, 3000);
}

// Temalar arası geçiş
function toggleTheme() {
    document.body.classList.toggle('dark-theme');
    const theme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
    localStorage.setItem('theme', theme);
}

// Sayfa yüklendiğinde tema durumu kontrol et
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme && savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
});
