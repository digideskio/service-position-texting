<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

assignmentsWasThereYes($_GET['id']);

header('Location: positions.php');
?>
