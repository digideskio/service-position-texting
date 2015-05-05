<?php
#!/usr/local/bin/php
require_once('/home/fwcm/public_html/fwcm_texting/config/database.php');
require_once('/home/fwcm/public_html/fwcm_texting/includes/database-functions.php');
require_once('/home/fwcm/public_html/fwcm_texting/includes/twilio-functions.php');

if(strtolower(php_sapi_name()) != 'cli')
	exit;

send_reminders();
?>