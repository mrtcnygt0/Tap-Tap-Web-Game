<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

session_start();
session_destroy();
header("Location: index.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            background: linear-gradient(to right, #141E30, #243B55);
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .logout-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 90%;
            max-width: 400px;
        }
        .logout-container h1 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h1>You have been logged out</h1>
        <p>Redirecting to the homepage...</p>
    </div>
</body>
</html>
