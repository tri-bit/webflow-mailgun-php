# Webflow Mailgun Php
Deliver seperate forms on your webflow site to different emails using a simple php endpoint.

## Installation

1. Copy the webflow/webflow-script.js contents to the Footer Code section of your site's custom code page (in the webflow designer.)
2. Upload the php code into a live folder on a standard php hosting service. (make sure to include the index.php to hide your directory contents)

## Setup - Webflow

1. Update the webflow script's _mailgunRelayEndpoint var to point to the live location of your mailgun-relay.php file.
```
var _mailgunRelayEndpoint = 'https://*yourendpointurl*/mailgun_relay.php';
```
2. Every form you wish to be relayed will need to add a custom <div> attribute of data-relay="true" added inside of the webflow form settings (see image below.)

3. Check and confirm you have a unique form name assigned to every Webflow form you want to relay. Also note the Relay attribute has been correctly added:

![alt text](https://github.com/tri-bit/webflow-mailgun-php/blob/master/docs/images/webflow-form-name.png?raw=true "Form Name")

## Setup - Php Server
1. Update everything inside of asterisks (*) in the settings.php file.
2. Update the relay destination array which maps the Webflow form names (Case Sensitive) to destination email addresses:
```
$relay_destinations = array(
        "Form Name 1" => ['example@emailaddress.com'],
        "Form Name 2" => ['example2@emailaddress.com', 'example@emailaddress.com'],
);
```

## Testing your Form Setup
By setting `$debug_destination_mode = true;` (inside settings.php) the form will not be sent on submission but will return the email destinations it would have gone to allowing you to confirm settings before going live. (Message will be viewable in the browser console.)

## Logging

The default setting `$relay_logging = true;` (inside settings.php) will log relay usage. (But not emails addresses or email content.)

Example logs:

```
Sunday, 10-May-20 22:03:47 UTC | error - destination not found for formSupport2
Sunday, 10-May-20 22:17:32 UTC | Quotes / Sales >  mailgun response: Queued. Thank you.
```

## Notice - Recaptcha is not re-validated by the server

Recaptcha tokens can only be validated once and Weblfow validates the token on the browser so there is currently no re-validation on the php server side. (So theoretically someone could get around recaptcha and use the relay code to spam emails specified in `$relay_destinations` )













