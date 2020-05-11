# Webflow Mailgun Php
Deliver seperate forms on your webflow site to different emails using a simple php endpoint.

## Installation

1. Copy the webflow/webflow-script.js contents to the Footer Code section of your site's custom code page (in the webflow designer.)
2. Upload the php code into a live folder on a standard php hosting service. (make sure to include the index.php to hide your directory contents)

## Setup

1. Update the webflow script's _mailgunRelayEndpoint var to point to the live location of your mailgun-relay.php file.
2. Update everything inside of asterisks (*) in the settings.php file.
3. Every form you wish to be relayed will need to add a Div attribute of data-relay="true" added inside of the webflow project.
![alt text](https://github.com/tri-bit/webflow-mailgun-php/blob/master/docs/images/webflow-settings-relay.png?raw=true "Relay Attribute")


## Testing your Form Setup
By setting `$debug_destination_mode = true;` (inside settings.php) the form will not be sent on submission but will return the email destinations it would have gone to allowing you to confirm settings before going live. (Message will be viewable in the browser console.)













