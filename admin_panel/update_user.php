<?php
include('db_connect.php');

// AJAX isteği ile gönderilen verileri alın
$userId = $_POST['userId'];
$field = $_POST['field'];
$value = $_POST['value'];

// Veritabanında kullanıcı bilgisini güncelleme işlemi
$updateQuery = "UPDATE users SET $field = '$value' WHERE id = $userId";
$conn->query($updateQuery);

// Başarılı bir şekilde güncellendiğine dair yanıt gönderin
echo 'Success';
?>
