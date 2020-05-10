<?php

include 'settings.php';
header("Content-Type: application/json");

$json = file_get_contents('php://input');

$data = JSON_decode($json);

if($data->wakeup == 'wakeup') {

    $wakeup = new stdClass();
    $wakeup->relay_ping_success = true;

    echo json_encode($wakeup);
    return;

}

else if($json) {
    submission_to_email($json);
} else {
    echo 'error - malformed submission';
}


function form_relay($form_title) {


    if(array_key_exists($form_title, $GLOBALS['relay_destinations'])) {
        return $GLOBALS['relay_destinations'][$form_title];
    }

    return null;

}



function submission_to_email($json) {

    $data = JSON_decode($json);


    if(!$data->formData) {

        echo json_encode($data);
        return false;
    }

    $email = '';
    $linebreak = PHP_EOL;

    foreach ($data->formData as $entry) {

            if(strcmp($entry->name, 'g-recaptcha-response')=== 0) { continue; }
            $email .= $linebreak . $entry->name . ' : ' . $entry->value;

    }


    $email .= $linebreak;

    //$verification = recaptchaV2Verify($data->formData);

    $destination = form_relay($data->formName);

    if($GLOBALS['debug_destination_mode']) {

        $debug_destination = new stdClass();
        $debug_destination->destination_debug_mode = 'destination would be ' . json_encode($destination) . ' - (mail not sent)';

        write_log('debug_log', $debug_destination->destination_debug_mode);

        echo json_encode($debug_destination);
        return;

    }



    if(!$destination) {
        $response = new stdClass();
        $error = 'send error - destination not found for form ' . $data->formName;
        $response->status = $error;

        if($GLOBALS['relay_logging']) {
        write_log('relay_log', $error);
        }

        echo json_encode($response);
        return false;
    }


    $toArray = $destination;
    $toname = RELAY_EMAIL_TO;
    $mailfromnane = RELAY_EMAIL_FROM_NAME;
    $mailfrom = RELAY_EMAIL_FROM;
    $subject = RELAY_EMAIL_SUBJECT;

    if(!empty($data->formName)) {
        $subject = $subject . ' : ' . $data->formName;
    }

    $html = '<div></div>';
    $text = $email;
    $tag = RELAY_EMAIL_TAG;
    $replyto = RELAY_EMAIL_REPLY_TO;


    $results = sendmailbymailgun($toArray,$toname,$mailfromnane,$mailfrom,$subject,$html,$text,$tag,$replyto);

    $response = new stdClass();
    $response->status = 'sent';
    //$response->mailgun_response = $results;
    $response->mailgun_response = $results['message'];
    $log = $data->formName . ' > ' . ' mailgun response: ' . $response->mailgun_response;

    if($GLOBALS['relay_logging']) {
        write_log('relay_log', $log);
    }


    echo JSON_encode($response);
    return $result;


}



function debug_log($dataString, $label) {

    $currentime = (int)(microtime() * 1000);
    $file = fopen('debug_logs/log_'.$currentime.$label.'.txt', "w");
    fwrite($file, $dataString);
    fclose($file);

}


function write_log($log_name, $log_entry) {


    if(!is_dir('logs')) {
        mkdir('logs', 0777, true);
    }
    $linebreak = PHP_EOL;


    //https://stackoverflow.com/questions/8655515/
    $timestamp = new \DateTime("now", new \DateTimeZone("UTC"));
    $entry = $timestamp->format(\DateTime::RFC850) . ' | ' . $log_entry . $linebreak;


    $filename = $log_name . '_' . date("Y") . '.txt';

    $file = fopen('logs/'.$filename, "a");
    fwrite($file, $entry);
    fclose($file);

}


//unused for now
/*
function recaptchaV2Verify($form_data) {

   //debug_log(json_encode($form_data), 'recaptcha_verification');

    $recaptchaVerificationEndpoint = 'https://www.google.com/recaptcha/api/siteverify';

    foreach($form_data as $entry) {

        if(strcmp("g-recaptcha-response", $entry->name) === 0) {

            if(strlen($entry->value) === 0) {


                echo 'error 57';
                return false;
            }

            $session = curl_init($recaptchaVerificationEndpoint);

            $post_array = array(

                'secret'=>RECAPTCHA_V2_SECRET_KEY,
                'response'=>$entry->value

            );

            curl_setopt($session, CURLOPT_POST, true);
            curl_setopt($session, CURLOPT_POSTFIELDS, $post_array);
            curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, true);

            $response = curl_exec($session);
            curl_close($session);


            //debug_log(json_encode($response), 'recaptcha_verification');


            return $response;
        }

    }



}
*/



//https://gist.github.com/swapnilshrikhande/d4c315b4a9590f4f91baba43a793f734

function sendmailbymailgun($toArray,$toname,$mailfromnane,$mailfrom,$subject,$html,$text,$tag,$replyto){

    $toFields = array();

    $combinedToAddress = '';

    foreach($toArray as $to) {

        array_push($toFields, $toname.'<'.$to.'>');
        $combinedToAddress .= $toname.'<'.$to.'>,';
    }


    $array_data = array(
		'from'=> $mailfromname .'<'.$mailfrom.'>',
    	//'to'=>$toname.'<'.$to.'>',
    	'to'=> $combinedToAddress,
		'subject'=>$subject,
		//'html'=>$html,
		'text'=>$text,
		'o:tracking'=>'yes',
		'o:tracking-clicks'=>'yes',
		'o:tracking-opens'=>'yes',
		'o:tag'=>$tag,
		'h:Reply-To'=>$replyto
    );




    $session = curl_init(MAILGUN_URL);
    curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  	curl_setopt($session, CURLOPT_USERPWD, 'api:'.MAILGUN_KEY);
    curl_setopt($session, CURLOPT_POST, true);
    curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($session);
    curl_close($session);
    $results = json_decode($response, true);
    return $results;

}




?>