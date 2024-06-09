<?php
require 'db_connect.php';

// Kullanıcıları seç
$usersQuery = "SELECT username, password, level, score, user_ref, ref_sayisi, where_ref, bot_level, last_login, task_davet, task_insta, task_twitter, task_spotify, game FROM users";
$usersResult = $conn->query($usersQuery);

// Kullanıcı bilgilerini HTML olarak biçimlendir
$html = '<table class="table">
            <thead>
                <tr>
                    <th>Kullanıcı Adı</th>
                    <th>Şifre</th>
                    <th>Seviye</th>
                    <th>Skor</th>
                    <th>Referans</th>
                    <th>Referans Sayısı</th>
                    <th>Referans Yeri</th>
                    <th>Bot Seviyesi</th>
                    <th>Son Giriş</th>
                    <th>Görev Davet</th>
                    <th>Görev Instagram</th>
                    <th>Görev Twitter</th>
                    <th>Görev Spotify</th>
					<th>Oyun</th>
                </tr>
            </thead>
            <tbody>';

if ($usersResult->num_rows > 0) {
    // Her bir kullanıcı için satır oluştur
    while ($row = $usersResult->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['username'] . '</td>
                    <td>' . $row['password'] . '</td>
                    <td>' . $row['level'] . '</td>
                    <td>' . $row['score'] . '</td>
                    <td>' . $row['user_ref'] . '</td>
                    <td>' . $row['ref_sayisi'] . '</td>
                    <td>' . $row['where_ref'] . '</td>
                    <td>' . $row['bot_level'] . '</td>
                    <td>' . $row['last_login'] . '</td>
                    <td>' . $row['task_davet'] . '</td>
                    <td>' . $row['task_insta'] . '</td>
                    <td>' . $row['task_twitter'] . '</td>
                    <td>' . $row['task_spotify'] . '</td>
					<td>' . $row['game'] . '</td>
                </tr>';
    }
} else {
    $html .= '<tr><td colspan="13">Kullanıcı bulunamadı.</td></tr>';
}

$html .= '</tbody></table>';

// Veritabanı bağlantısını kapat
$conn->close();

// HTML tablosunu yazdır
echo $html;
?>
