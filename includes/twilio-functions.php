<?php
// Texting functions and includes
require_once('/home/fwcm/public_html/service-position-texting/includes/twilio/Services/Twilio.php');


// Send an SMS message through Twilio
function send_sms($to_number, $text)
{
	// Globals
	global $TWILIO_ACCOUNT_SID, $TWILIO_AUTH_TOKEN, $TWILIO_NUMBER;
	
	
	// Create client
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