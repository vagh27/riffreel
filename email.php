<?php

	// Email the submission
	$postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    @$email = $request->email;
    @$key = $request->key;
    @$expectation = $request->expectation;
    @$specify = $request->specify;
    @$example = $request->example;
    @$love = $request->love;

    // Email to Admin
	$to = 'jbdunlea@gmail.com';
	$from = $email;
	$subject = "RIFF VIDEO SUBMISSION";

	$message = "<html>
	  <body bgcolor=\"#DCEEFC\">
		Email: " . $email . "<br />
		Key: " . $key . "<br />
		Expectation: " . $expectation . "<br />
		Specify: " . $specify . "<br />
		Example: " . $example . "<br />
		Love: " . $love . "<br />
	  </body>
	</html>";

	$headers  .= "From: " . $email . "\r\n";
	$headers  .= "Content-type: text/html\r\n";

	mail($to, $subject, $message, $headers);

	// Email to USER
	$to = $email;
	$from = 'john@thereactory.com';
	$subject = "Your riffreel upload is complete!";

	$message = "<html>
	  <body bgcolor=\"#DCEEFC\">
		Hey there!
		<br /><br />
		Thanks for starting your riffreel project! Your files have successfully been uploaded and one of our producers will begin working on your riffreel video very soon. All you need to do now is sit back and relax - As soon as it is complete, we will email you!
		<br /><br />
		Should we need anything else or have further questions for you we will let you know.
		<br /><br />
		If you have any questions or concerns please <a href='http://riffreel.com'>contact us!</a>
		<br /><br />
		Thanks!
		The riffreel team.
		<br /><br />
		<a href='http://riffreel.com'>http://www.riffreel.com</a><br />
		<a href='http://www.facebook.com/riffreel'>http://www.facebook.com/riffreel</a><br />
		<a href='http://instagram.com/riffreel'>http://instagram.com/riffreel</a>
	  </body>
	</html>";

	$headers  .= "From: vagh27@gmail.com\r\n";
	$headers  .= "Content-type: text/html\r\n";

	mail($to, $subject, $message, $headers);

?>