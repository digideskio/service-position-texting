<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/variables.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/database-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/twilio-functions.php');


// Get data
$cell_phone = intval(trim(str_replace('+1', '', $_REQUEST['From'])));
$body = strtoupper(trim($_REQUEST['Body']));


// Process response
if($body == 'YES')
{
	$sayings = array('Sounds great boss!', 'Awesome, have a great rest of the day!', 'Roger that. Over and out.', 'Thanks for letting us know!', 'See you there!', 'Aight, sounds good yo\'', 'Ronny is proud.', 'Perfect! Thanks.', 'Got it, we\'ll see you there.');
	assignmentsConfirmedTextYes($cell_phone);
	respond($sayings[rand(0, count($sayings) - 1)].PHP_EOL.'-FWCM');
}
else if($body == 'NO')
{
	assignmentsConfirmedTextNo($cell_phone);
	respond('Thanks, we\'ll be in touch with you soon.'.PHP_EOL.'-FWCM');
}
else
{
	respond('Whoa bro, that\'s not a YES or a NO!'.PHP_EOL.'-FWCM');
}


// Respond
function respond($msg){
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<Response>';
		echo '<Message>'.$msg.'</Message>';
    echo '</Response>';
    exit;
}
?>
