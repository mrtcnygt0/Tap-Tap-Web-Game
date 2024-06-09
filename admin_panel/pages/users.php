<?php
include('db_connect.php');

// Veritabanındaki veriyi güncelleme işlevi
if (isset($_POST['userId']) && isset($_POST['field']) && isset($_POST['value'])) {
    $userId = $_POST['userId'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    $updateQuery = "UPDATE users SET $field = '$value' WHERE id = $userId";
    $conn->query($updateQuery);
    exit();
}

$usersQuery = "SELECT id, username, password, level, score, user_ref, ref_sayisi, where_ref, bot_level, last_login, task_davet, task_insta, task_twitter, task_spotify, energy, energy_level, game FROM users";
$usersResult = $conn->query($usersQuery);
?>
<head>
    <meta http-equiv="refresh" content="15">
    <style>
        .editable {
            cursor: pointer;
        }
        .editable input {
            width: 100%;
            border: none;
            outline: none;
        }
        .table-container {
            overflow-x: auto;
            white-space: nowrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<h2>Kullanıcılar</h2>
<div class="table-container">
    <table id="usersTable" class="table">
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
                <th>Oyun</th>
				<th>Görev Davet</th>
                <th>Görev Instagram</th>
                <th>Görev Twitter</th>
                <th>Görev Spotify</th>
                <th>Enerji</th>
                <th>Enerji Level</th>
				
            </tr>
        </thead>
        <tbody>
            <?php while($row = $usersResult->fetch_assoc()): ?>
                <tr>
                    <td class="editable" data-field="username" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['username']; ?></td>
                    <td class="editable" data-field="password" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['password']; ?></td>
                    <td class="editable" data-field="level" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['level']; ?></td>
                    <td class="editable" data-field="score" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['score']; ?></td>
                    <td class="editable" data-field="user_ref" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['user_ref']; ?></td>
                    <td class="editable" data-field="ref_sayisi" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['ref_sayisi']; ?></td>
                    <td class="editable" data-field="where_ref" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['where_ref']; ?></td>
                    <td class="editable" data-field="bot_level" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['bot_level']; ?></td>
                    <td class="editable" data-field="last_login" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['last_login']; ?></td>
                    <td class="editable" data-field="game" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['game']; ?></td>
					<td class="editable" data-field="task_davet" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['task_davet']; ?></td>
                    <td class="editable" data-field="task_insta" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['task_insta']; ?></td>
                    <td class="editable" data-field="task_twitter" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['task_twitter']; ?></td>
                    <td class="editable" data-field="task_spotify" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['task_spotify']; ?></td>
                    <td class="editable" data-field="energy" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['energy']; ?></td>
                    <td class="editable" data-field="energy_level" data-user-id="<?php echo $row['id']; ?>"><?php echo $row['energy_level']; ?></td>
					
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
		const editableCells = document.querySelectorAll('.editable');

		editableCells.forEach(cell => {
			cell.addEventListener('click', function() {
				const input = document.createElement('input');
				input.value = this.innerText;
				this.innerText = '';
				this.appendChild(input);
				input.focus();
			});
		});

		// Enter tuşuna basıldığında veri güncellemesini sağlayan fonksiyon
		function handleEnterKeyPress(event) {
			if (event.key === 'Enter') {
				const activeCell = document.querySelector('.editable input:focus');
				if (activeCell) {
					const newValue = activeCell.value;
					const fieldName = activeCell.parentElement.dataset.field;
					const userId = activeCell.parentElement.dataset.userId;
					updateValue(userId, fieldName, newValue);
					activeCell.blur(); // Seçimi iptal et
				}
			}
		}

		document.addEventListener('keydown', handleEnterKeyPress);

		// AJAX ile veriyi güncelleme işlevi
		function updateValue(userId, field, value) {
			const formData = new FormData();
			formData.append('userId', userId);
			formData.append('field', field);
			formData.append('value', value);

			fetch('update_user.php', {
				method: 'POST',
				body: formData
			})
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.text();
			})
			.then(data => {
				console.log('Value updated successfully:', data);
				// Başarılı güncelleme durumunda, kullanıcı arayüzünü güncelleyebilirsiniz
			})
			.catch(error => {
				console.error('Error updating value:', error);
			});
		}
	});

</script>
