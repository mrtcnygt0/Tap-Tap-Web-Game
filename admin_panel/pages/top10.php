<?php
$top10Query = "SELECT username, score FROM users ORDER BY score DESC LIMIT 10";
$top10Result = $conn->query($top10Query);
?>
<head>
	<meta http-equiv="refresh" content="15">
	<script>
	setInterval(function() {
		$.ajax({
			url: 'pages/users.php',
			success: function(data) {
				$('#content').html(data);
			}
		});
	}, 1000);
</script>
</head>
<h2>Top 10</h2>
<table class="table">
    <thead>
        <tr>
            <th>Sıra</th>
            <th>Kullanıcı Adı</th>
            <th>Skor</th>
        </tr>
    </thead>
    <tbody>
        <?php $rank = 1; ?>
        <?php while($row = $top10Result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $rank++; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['score']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
