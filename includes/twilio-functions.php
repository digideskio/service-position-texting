<?php
// Texting functions and includes
require_once('/home/fwcm/public_html/fwcm_texting/includes/twilio/Services/Twilio.php');

function send_sms($to_number, $text){
	// Global
	$TWILIO_ACCOUNT_SID, $TWILIO_AUTH_TOKEN, $TWILIO_NUMBER;
	
	
	$client = new Services_Twilio($TWILIO_ACCOUNT_SID, $TWILIO_AUTH_TOKEN);
	
	try
	{
		$client->account->messages->create(array(
			'To' => $to_number,
			'From' => $TWILIO_NUMBER,
			'Body' => $text,
		));
	}
	catch(Exception $e)
	{
		// Just ignore
	}
}
?>