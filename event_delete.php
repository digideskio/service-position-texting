<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

eventsDelete($_GET['id']);

header('Location: events.php');
?>
