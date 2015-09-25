<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

assignmentsWasThereNo($_GET['id']);

if($_GET['check_in'])
	header('Location: check_in.php');
else
	header('Location: positions.php');
?>
