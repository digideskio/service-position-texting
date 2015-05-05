<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

$num = eventsSendConfirmationTexts($_GET['id']);

$_SESSION['message'] = '<strong>'.$num.'</strong> texts have been sent!';

header('Location: events.php');
?>
