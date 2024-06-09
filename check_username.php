<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

// Include your database connection file
include 'db_connect.php';

// Assume $_POST['username'] contains the username sent from JavaScript
$username = $_POST['username'];

// Check if username already exists in the database
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Username already exists
    echo json_encode(array('exists' => true));
} else {
    // Username does not exist
    echo json_encode(array('exists' => false));
}

// Close database connection
$conn->close();
?>
