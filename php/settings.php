<?php

date_default_timezone_set("America/Chicago");
header("Access-Control-Allow-Origin: *");

define ('MAILGUN_URL', 'https://api.mailgun.net/v3/*YOUR MAILGUN SENDING EMAIL ADDRESS*/messages');
define ('MAILGUN_KEY', '*YOUR MAILGUN API KEY *');

define ('RELAY_EMAIL_TO', '*EMAIL TO*');
define ('RELAY_EMAIL_FROM', '*FROM EMAIL ADDRESS*');
define ('RELAY_EMAIL_FROM_NAME', '*EMAIL FROM NAME*');
define ('RELAY_EMAIL_SUBJECT', '*EMAIL SUBJECT*'); //email subject will be combined with form title
define ('RELAY_EMAIL_REPLY_TO', '*REPLY TO EMAIL ADDRESS*');
define ('RELAY_EMAIL_TAG', '*MAILGUN TAG*'); //tag keyword for mailgun internal tracking


$relay_destinations = array(

        //replace example below
        //webflow form name => [emails]
        "Form Name 1" => ['example@emailaddress.com'],
        "Form Name 2" => ['example2@emailaddress.com', 'example@emailaddress.com'],
        "Form Name 3" => ['example2@emailaddress.com', 'example3@emailaddress.com']

);

$debug_destination_mode = false;
$relay_logging = true;

?>