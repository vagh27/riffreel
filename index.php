<?php

	/*
	 * Calculate HMAC-SHA1 according to RFC2104
	 * See http://www.faqs.org/rfcs/rfc2104.html
	 */
	function hmacsha1($key,$data) {
		$blocksize=64;
		$hashfunc='sha1';
		if (strlen($key)>$blocksize)
			$key=pack('H*', $hashfunc($key));
		$key=str_pad($key,$blocksize,chr(0x00));
		$ipad=str_repeat(chr(0x36),$blocksize);
		$opad=str_repeat(chr(0x5c),$blocksize);
		$hmac = pack(
					'H*',$hashfunc(
						($key^$opad).pack(
							'H*',$hashfunc(
								($key^$ipad).$data
							)
						)
					)
				);
		return bin2hex($hmac);
	}

	/*
	 * Used to encode a field for Amazon Auth
	 * (taken from the Amazon S3 PHP example library)
	 */
	function hex2b64($str)
	{
		$raw = '';
		for ($i=0; $i < strlen($str); $i+=2)
		{
			$raw .= chr(hexdec(substr($str, $i, 2)));
		}
		return base64_encode($raw);
	}

	/* Create the Amazon S3 Policy that needs to be signed */
	$policy = '{
    	"expiration": "2020-01-01T00:00:00Z",
	  	"conditions": [
		    {"bucket": "riff"},
		    ["starts-with", "$key", "uploads/"],
		    {"acl": "public-read"}
	  	]
	}';

	/*
	 * Base64 encode the Policy Document and then
	 * create HMAC SHA-1 signature of the base64 encoded policy
	 * using the secret key. Finally, encode it for Amazon Authentication.
	 */
	$base64_policy = base64_encode($policy);
	$signature = hex2b64(hmacsha1('NcxAEy1Ewzi9/cEsFlH7rnplkPCFqTHicFMugAWT', $base64_policy));

?>
<!DOCTYPE html>
<html id="ng-app" ng-app="Riff"> <!-- id="ng-app" IE<8 -->

	<head>
		<title>RIFF Downloader</title>
		<link href='http://fonts.googleapis.com/css?family=Raleway:500, 700,400,300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/base.css?v1">

		<!-- Fix for old browsers -->
		<script src="js/lib/es5-shim/es5-shim.min.js"></script>
		<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
		<script src="js/console-sham.js"></script>

		<script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
		<script src="http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/hmac-sha1.js"></script>
		<script src="http://crypto-js.googlecode.com/svn/tags/3.0.2/build/components/enc-base64-min.js"></script>

		<!--[if lt IE 9]>
			<script src="js/html5shiv.js"></script>
		<![endif]-->

		<script type="text/javascript">
			if (screen.width <= 699) {
				window.top.location.href = "http://www.riffreel.com/mobile/getstarted";
			}
		</script>
		<script src="js/lib/angular/angular.min.js"></script>
		<script src="js/lib/angular-file-upload/angular-file-upload.js"></script>

	</head>

	<body ng-controller="AppController" nv-file-drop="" uploader="uploader" filters="queueLimit, customFilter">

		<input type="hidden" ng-model="policy" ng-init="policy = '<?php echo $base64_policy ?>'">
		<input type="hidden" ng-model="signature" ng-init="signature = '<?php echo $signature ?>'">

		<div ng-cloak ng-show="status.text" class="status-bar {{status.color}}" ng-cloak>
			<span>{{status.text}}</span>
			<div class="btn-close-knockout" ng-click="status = {}"></div>
		</div>

		<header>
			<img src="img/logo.png" alt="riffreel" class="logo">
			<nav>
				<li><a href="http://www.riffreel.com/" target="_parent">HOME</a></li>
				<li><a href="http://www.riffreel.com/explorework/" target="_parent">EXPLORE VIDEOS</a></li>
				<li><a href="http://www.riffreel.com/howitworks" target="_parent">HOW IT WORKS</a></li>
				<li><a href="http://www.riffreel.com/tipsntricks/" target="_parent">TIPS &amp; TRICKS</a></li>
				<li><a href="#" class="round-btn">GET STARTED</a></li>
			</nav>
			<div class="clear"></div>
			<h1>Lets get creative<span>Send us your videos to get started</span></h1>
			<a class="anchor" href="#Upload">&#9662;</a>
		</header>

		<div class="container" id="Upload">

			<div class="row">

				<div class="col-md-12">

					<h3>Your info</h3>

					<div class="form-group">
						<label for="project">Project Name*</label>
						<input type="project" class="form-control" id="project" name="project" ng-model="project" placeholder="E.g. My Trip to Alaska">
					</div>

					<div class="form-group">
						<label for="email">Email address*</label>
						<input type="email" class="form-control" id="email" name="email" ng-model="email" placeholder="john@doe.com">
					</div>

					<div class="form-group">
						<label for="confirm-email">Confirm Email address*</label>
						<input type="email" class="form-control" name="confirm-email" ng-model="confirmEmail">
					</div>

					<h3>Know what you want? Tell us! <span>(optional)</span></h3>

					<div class="form-group">
						<label for="expectation">
							How should your riffreel look?
							<i>i
								<span>Don't know what you want, no problem! However the more direction we have the better!</span>
							</i>
						</label>
						<input type="text" class="form-control" id="expectation" name="expectation" ng-model="expectation" placeholder="E.g. - A fast paced movie with a lot of color effects that shows every experience on our trip to Hawaii">
					</div>

					<div class="form-group">
						<label for="specify">Let us know what we must include.</label>
						<textarea class="form-control" id="specify" name="specify" ng-model="specify" placeholder="E.g. Please include the couple of time lapse clips of the New York City skyline."></textarea>
					</div>

					<div class="form-group">
						<input type="checkbox" id="instagram" ng-model="instagram" ng-true-value="'Yes'" ng-false-value="'No'">
						<label for="instagram">
							 Include a 15 second instagram-able version of my riffreel video! + $5.99
						</label>
					</div>

					<h3>Upload your footage</h3>

					<div ng-hide="uploader.isUploading">
						<div ng-show="uploader.isHTML5">
							<!-- 3. nv-file-over uploader="link" over-class="className" -->
							<div class="well my-drop-zone" nv-file-over="" uploader="uploader">
								Drag videos here
							</div>

							<!-- Example: nv-file-drop="" uploader="{Object}" options="{Object}" filters="{String}"
							<div nv-file-drop="" uploader="uploader" options="{ url: '/foo' }">
								<div nv-file-over="" uploader="uploader" over-class="another-file-over-class" class="well my-drop-zone">
									Another drop zone with its own settings
								</div>
							</div>-->
						</div>

						<!-- Example: nv-file-select="" uploader="{Object}" options="{Object}" filters="{String}" -->
						<label for="files" class="btn btn-info">Choose files to upload</label>
						<input type="file" nv-file-select="" id="files" uploader="uploader" multiple style="visibility: hidden"  /><br/>

						<!-- Single
						<input type="file" nv-file-select="" uploader="uploader" /> -->
					</div>
				</div>

				<div class="col-md-12" style="margin-bottom: 40px" ng-show="uploader.queue.length > 0">

					<p>Total files: {{ uploader.queue.length }}</p>

					<table class="table">
						<thead>
							<tr>
								<th width="50%">Name</th>
								<th ng-show="uploader.isHTML5">Size</th>
								<th ng-show="uploader.isHTML5">Progress</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="item in uploader.queue">
								<td><strong>{{ item.file.name }}</strong></td>
								<td ng-show="uploader.isHTML5" nowrap>{{ item.file.size/1024/1024|number:2 }} MB</td>
								<td ng-show="uploader.isHTML5">
									<div class="progress" style="margin-bottom: 0;">
										<div class="progress-bar" role="progressbar" ng-style="{ 'width': item.progress + '%' }"></div>
									</div>
								</td>
								<td class="text-center">
									<span ng-show="item.isUploading">In progress</span>
									<span ng-show="!item.isUploading && !item.isSuccess && !item.isCancel && !item.isError">Ready</span>
									<span ng-show="item.isSuccess">Completed <i class="glyphicon glyphicon-ok"></i></span>
									<span ng-show="item.isCancel">Cancelled <i class="glyphicon glyphicon-ban-circle"></i></span>
									<span ng-show="item.isError">Error <i class="glyphicon glyphicon-remove"></i></span>
								</td>
								<td nowrap>
									<!--<button type="button" class="btn btn-success btn-xs" ng-click="item.upload(); test(item)" ng-show="(item.isCancel && !item.isReady) || (!item.isSuccess && item.isUploaded)">
										<span class="glyphicon glyphicon-upload"></span> Retry
									</button>-->
									<button type="button" class="btn btn-warning btn-xs" ng-click="item.cancel()" ng-disabled="!item.isUploading">
										<span class="glyphicon glyphicon-ban-circle"></span> Cancel
									</button>
									<button type="button" class="btn btn-danger btn-xs" ng-click="item.remove()">
										<span class="glyphicon glyphicon-trash"></span> Remove
									</button>
								</td>
							</tr>
						</tbody>
					</table>

					<!--<div>
						<button type="button" class="btn btn-success btn-s" ng-click="uploader.uploadAll()" ng-disabled="!uploader.getNotUploadedItems().length">
							<span class="glyphicon glyphicon-upload"></span> Upload all
						</button>
						<button type="button" class="btn btn-warning btn-s" ng-click="uploader.cancelAll()" ng-disabled="!uploader.isUploading">
							<span class="glyphicon glyphicon-ban-circle"></span> Cancel all
						</button>
						<button type="button" class="btn btn-danger btn-s" ng-click="uploader.clearQueue()" ng-disabled="!uploader.queue.length">
							<span class="glyphicon glyphicon-trash"></span> Remove all
						</button>
					</div>-->



				</div>

				<div class="col-md-12">

					<!-- <div ng-show="uploading">
						Queue progress:
						<div class="progress" style="">
							<div class="progress-bar" role="progressbar" ng-style="{ 'width': uploader.progress + '%' }"></div>
						</div>
					</div> -->

					<button
						type="button"
						class="btn btn-success btn-s"
						ng-click="submit()"
						ng-disabled="uploader.getNotUploadedItems().length === 0"
						ng-hide="uploader.isUploading">
						<span class="glyphicon glyphicon-upload"></span> Upload & Submit
					</button>

					<button type="button" class="btn btn-warning btn-s" ng-click="cancelAll = true; uploader.cancelAll();" ng-show="uploader.isUploading">
						<span class="glyphicon glyphicon-ban-circle"></span> Cancel all
					</button>
				</div>
			</div>
		</div>

		<footer>
			<ul class="social-icons">
				<li><a href="http://www.facebook.com/riffreel" target="_blank"
					style="background-image:url(img/icon_fb.jpg); background-size: auto 20px;">
				</a></li>
				<li><a href="http://instagram.com/riffreel" target="_blank"
					style="background-image:url(img/icon_instagram.jpg); background-size: auto 16px;">
				</a></li>
				<li><a href="https://twitter.com/riffreel" target="_blank"
					style="background-image:url(img/icon_twitter.jpg); background-size: auto 14px;">
				</a></li>
				<li><a href="mailto:email@riffreel.com"
					style="background-image:url(img/icon_email.jpg); background-size: auto 12px;">
				</a></li>
			</ul>
			<ul>
				<li><a href="http://www.riffreel.com/" target="_parent">HOME</a></li>
				<li><a href="http://www.riffreel.com/howitworks" target="_parent">HOW IT WORKS</a></li>
				<li><a href="http://www.riffreel.com/contact" target="_parent">CONTACT US</a></li>
				<li><a href="http://www.riffreel.com/terms" target="_parent">TERMS</a></li>
			</ul>
		</footer>

		<modal-dialog show="success" width="600px" height="auto" ng-cloak cant-close="true">
			<h1>Congratulations! Your upload is complete!</h1>
			<p>Click the FINISH button below to return to the main site and wait to receive a confirmation email from us!</p>
			<a class="round-btn" target="_parent" href="http://www.riffreel.com/">FINISH</a>
			<p>Other questions or concerns? Please <a href="http://www.riffreel.com/contact" target="_parent">Contact us!</a></p>
		</modal-dialog>

		<modal-dialog show="problems" width="600px" height="auto" ng-cloak>
			<h1>Congratulations! Your upload is complete!</h1>
			<p>However, it looks like some of your uploads were either cancelled or did not upload correctly.</p>

			<table cellpadding="10" style="width: 100%;">
				<tr>
					<td style="width: 50%"><a style="margin-left: 0;" href ng-click="close(clear)" class="round-btn">Back to upload form</a></td>
					<td><a style="margin-left: 0;" class="round-btn" target="_parent" href="http://www.riffreel.com/">I'm finished</a></td>
				</tr>
			</table>

			<p>Other questions or concerns? Please <a href="http://www.riffreel.com/contact" target="_parent">Contact us!</a></p>
		</modal-dialog>

		<modal-dialog show="uploadForm" width="600px" height="auto" ng-cloak>
			<h1>Your upload has started!</h1>
			<p>This process may take some time, depending on the amount of footage you are uploading.  Hit "Got it!" below and leave this page open until the upload is complete.</p>
			<p>Thanks for your patience!</p>
			<a href ng-click="close()" class="round-btn">Got It!</a>
		</modal-dialog>

		<script src="js/file-types.js"></script>
		<script src="js/app.js?v2"></script>
		<script>
  		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-64923175-1', 'auto');
		  ga('send', 'pageview');

		</script>


	</body>
</html>
