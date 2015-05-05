<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

eventsFillPositions($_GET['id']);

$_SESSION['message'] = 'The positions have been filled!';

header('Location: events.php');
?>
