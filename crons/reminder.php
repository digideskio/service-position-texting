<?php
#!/usr/local/bin/php
require_once('/home/fwcm/public_html/service-position-texting/config/variables.php');
require_once($BASE_PATH.'/config/database.php');
require_once($BASE_PATH.'/includes/database-functions.php');
require_once($BASE_PATH.'/includes/twilio-functions.php');

if(strtolower(php_sapi_name()) != 'cli')
	exit;

send_reminders();
?>