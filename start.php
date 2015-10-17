<?php

	// Email the submission
	//$postdata = file_get_contents("php://input");
    //$request = json_decode($postdata);


	// Email to Admin
	//$to = 'email@riffreel.com';
	$to = 'vagh27@gmail.com';
	$from = 'vagh27@yahoo.com';
	$subject = "RIFF VIDEO SUBMISSION COMPLETED";

	$message = "<html>
	  <body bgcolor=\"#DCEEFC\">
		test
	  </body>
	</html>";

	$headers  .= "From: " . $from . "\r\n";
	$headers  .= "Content-type: text/html\r\n";

	mail($to, $subject, $message, $headers);

?>