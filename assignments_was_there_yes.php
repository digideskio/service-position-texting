<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

assignmentsWasThereYes($_GET['id']);

if($_GET['check_in'])
	header('Location: check_in.php');
else
	header('Location: positions.php');
?>
