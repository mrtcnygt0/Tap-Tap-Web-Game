<?php

/*!
  * Item: Tap-Tap GAME
  * Description: Web Tabanlı Mobil Oyun
  * Author/Developer: mrtcnygt0
  * Version: v1
*/

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>
