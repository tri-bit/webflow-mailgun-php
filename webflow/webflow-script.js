//project footer code (add code before </body> tag)
//replace _mailgunRelayEndpoint url with your server's php file

<script>

var _mailgunRelayEndpoint = 'https://*yourendpointurl*/mailgun_relay.php';

Webflow.push(function() {

  mailgun_wake();
  mailgun_init();

});

function mailgun_wake() {

   $.ajax({
        type: "POST",
        url: _mailgunRelayEndpoint,
        data: JSON.stringify({wakeup:'wakeup'}),
        success: function(response) {

          console.log(response);
        },
        error: function(error) {
          console.log(error);
        },
        dataType: 'json'
   });



}

function mailgun_init() {

  $('form').each(function() {


    var relay = $(this).parent().data('relay');

    if(!relay) {

     	//relay not specified for form - ignoring
      	return true;
    }


    $(this).submit(function(event) {

      console.log('relay form submit');
      //event.preventDefault();

      var name = $(this).data('name') || null;

      formData = $(this).serializeArray();

      var data = JSON.stringify({formName:name, formData:formData});

      var recaptcha_required = false;
      var recaptcha_response = null;

      formData.forEach(function(d) {

      	if(d.name == "g-recaptcha-response") {

          	recaptcha_required = true;

          	if(d.value !== "") {
              	recaptcha_response = d.value
            	//console.log('recaptcha found: ' + recaptcha_response);
            }

        }

      });



      if(!recaptcha_response && recaptcha_required) {

      	console.log('recaptcha found but required, form send cancelled');
        return;

      }	 else if(!recaptcha_response && !recaptcha_required) {

      	 console.log('recaptcha info not found but not required');

      } else if(recaptcha_response && recaptcha_required) {

       	 console.log('recaptcha required & found, sending');
      }


      console.log('sending to mailgun');

      $.ajax({
        type: "POST",
        url: _mailgunRelayEndpoint,
        data: data,
        success: mailgun_success,
        error: function(error) {
          console.log(error);
        },
        dataType: 'json'
      });


    });

  });

}

function mailgun_success(response) {

  console.log('mailgun response', response);


}


</script>