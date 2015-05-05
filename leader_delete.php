<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

leadersDelete($_GET['id']);

header('Location: leaders.php');
?>
