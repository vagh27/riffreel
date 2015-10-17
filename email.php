<?php

	// Email the submission
	$postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    @$email = $request->email;
    @$project = $request->project;
    @$key = $request->key;
    @$expectation = $request->expectation;
    @$specify = $request->specify;
    @$instagram = $request->instagram;

	// Email to USER
	$to = $email;
	$from = 'email@riffreel.com';
	$subject = "Your riffreel upload is complete!";

	$message = "<html>
	  <body bgcolor=\"#DCEEFC\">
		Hi,
		<br /><br />
		Thanks for starting your riffreel project!
		<br /><br />
		Your files have successfully been uploaded and one of our producers will begin working on your riffreel video very soon. All you need to do now is sit back and relax - As soon as it is complete, we will email you.
		<br /><br />
		Should we need anything else or have further questions for you we will let you know.
		<br /><br />
		If you have any questions or concerns please <a href='http://riffreel.com/contact'>contact us!</a>
		<br /><br />
		Thanks!<br />
		The riffreel team.
		<br /><br />
		<a href='http://riffreel.com'>http://www.riffreel.com</a><br />
		<a href='http://www.facebook.com/riffreel'>http://www.facebook.com/riffreel</a><br />
		<a href='http://instagram.com/riffreel'>http://instagram.com/riffreel</a>
	  </body>
	</html>";

	$headers  .= "From: email@riffreel.com\r\n";
	$headers  .= "Content-type: text/html\r\n";

	mail($to, $subject, $message, $headers, "-f email@riffreel.com");

	// Email to Admin
	$to = 'email@riffreel.com';
	$from = $email;
	$subject = "RIFF VIDEO SUBMISSION";

	$message = "<html>
	  <body bgcolor=\"#DCEEFC\">
		Email: " . $email . "<br />
		Project: " . $project . "<br />
		Key: " . $key . "<br />
		Expectation: " . $expectation . "<br />
		Specify: " . $specify . "<br />
		Add Instagram: " . $instagram . "<br />
	  </body>
	</html>";

	$headers  .= "From: " . $email . "\r\n";
	$headers  .= "Content-type: text/html\r\n";

	mail($to, $subject, $message, $headers);

?>