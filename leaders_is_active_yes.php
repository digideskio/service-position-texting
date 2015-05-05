<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

leadersIsActiveYes($_GET['id']);

header('Location: leaders.php');
?>
