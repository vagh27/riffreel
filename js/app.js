// Main Application
var Riff = angular.module('Riff', ['angularFileUpload']);

(function(ng, app, $) {

	'use strict';

	app.controller('AppController', ['$scope', '$http', '$filter', 'FileUploader', function($scope, $http, $filter, FileUploader) {

		// Upres BG image
		var src = "img/bg_full_high.jpg",
			img = new Image();
		$(img).load(function() {
			$('header').css('background-image', 'url(' + src + ')');
		}).attr("src", src);

		//smooth scroll
		$('a[href*=#]:not([href=#])').click(function() {
		    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
		      var target = $(this.hash);
		      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
		      if (target.length) {
		        $('html,body').animate({
		          scrollTop: target.offset().top
		        }, 500);
		        return false;
		      }
		    }
		});

		$scope.status = {};
		$scope.completed = [];

		$scope.test = function(e){
			console.log(e)
		};

		function validateEmail() {
			var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test($scope.email);
		}

		var uploader = $scope.uploader = new FileUploader({
			url: 'https://riff.s3.amazonaws.com/'
		});

		// FILTERS
		uploader.filters.push({
			name: 'customFilter',
			fn: function(item /*{File|FileLikeObject}*/, options) {
				return this.queue.length < 100;
			}
		});

		function checkType(type) {
			return fileTypes.indexOf(type) > -1 ? true : false;
		}

		// CALLBACKS
		uploader.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
			//console.info('onWhenAddingFileFailed', item, filter, options);
		};
		uploader.onAfterAddingFile = function(fileItem) {

			var file = fileItem._file;

			// clear error message
			$scope.status = {};

			// check file type
			if (!checkType(file.type)){
				$scope.status.text = 'Please add an appropriate image or video file type.';
				$scope.uploader.removeFromQueue(fileItem);
			};

			// check file size
			if (file.size > 5000000000){
				$scope.status.text = 'Files must be less than 5GB each.';
				$scope.uploader.removeFromQueue(fileItem);
			};
		};
		uploader.onAfterAddingAll = function(addedFileItems) {
			//console.info('onAfterAddingAll', addedFileItems);
		};
		uploader.onBeforeUploadItem = function(item) {
			$scope.key = $scope.email ? $scope.email.slice(0, $scope.email.indexOf("@")) : 'undefined';
			$scope.project = $filter('friendlyUrl')($scope.project);
			item.formData.push({
				key: $scope.email + '/' + $scope.project + '/' + item._file.name,
				email: $scope.email
			});
		};
		uploader.onProgressItem = function(fileItem, progress) {
			//console.info('onProgressItem', fileItem, progress);
			$scope.progress = progress;
		};
		uploader.onProgressAll = function(progress) {
			//console.info('onProgressAll', progress);
		};
		uploader.onSuccessItem = function(fileItem, response, status, headers) {
			console.info('onSuccessItem', fileItem, response, status, headers);
			$scope.completed.push(fileItem);
		};
		uploader.onErrorItem = function(fileItem, response, status, headers) {
			console.info('onErrorItem', fileItem, response, status, headers);
		};
		uploader.onCancelItem = function(fileItem, response, status, headers) {
			//console.info('onCancelItem', fileItem, response, status, headers);
		};
		uploader.onCompleteItem = function(fileItem, response, status, headers) {
			//console.info('onCompleteItem', fileItem, response, status, headers);
			//$scope.completed.push(fileItem);
		};

		uploader.onCancelItem = function(fileItem, response, status, headers) {
			//console.info('CANCEL', fileItem, response, status, headers);
			$scope.cancelled = true;
		};


		uploader.onCompleteAll = function() {
			if (!$scope.cancelAll && $scope.completed.length > 0) {
				$scope.success = true;
				$http({
					method: 'POST',
					url: 'email.php',
					data: {
						key: $scope.key,
						email: $scope.email,
						expectation: $scope.expectation,
						specify: $scope.specify,
						example: $scope.example,
						love: $scope.love
					},
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
				}).
				success(function(data, status, headers, config) {
					console.log('Emails Sent');
				}).
				error(function(data) {
					console.log('Emails Not Sent')
				});
			} else {
				$scope.uploader.clearQueue();
			}
		};

		$scope.submit = function(){

			$scope.status = {};

			if (!validateEmail()) {
				$scope.status.text = "Please enter a valid email."
				return false;
			}

			if (!$scope.project) {
				$scope.status.text = "Please enter a project name."
				return false;
			}

			if ($scope.uploader.getNotUploadedItems().length === 0){
				$scope.status.text = "Please provide at least one video clip for upload."
				return false;
			};

			$scope.uploading = true;
			$scope.uploader.uploadAll();
		}
	}]);

})(angular, Riff, jQuery);


(function(ng, app) {

	'use strict';

	app.directive('modalDialog', function() {
		return {
			restrict: 'E',
			scope: {
				show: '=',
				form: '='
			},
			replace: true,
			transclude: true,
			link: function(scope, element, attrs) {
				scope.dialogStyle = {};
				if (attrs.width) {
					scope.dialogStyle.width = attrs.width;
				}
				if (attrs.height) {
					scope.dialogStyle.height = attrs.height;
				}
				if (attrs.cantClose) {
					scope.cantclose = true;
				}
				scope.hideModal = function(e) {
					if (e === true){
						scope.show = false;
					} else if (e.target.className === 'modal-cont' && !attrs.cantClose){
						scope.show = false;
					}
				};
			},
			templateUrl: 'js/components/modalTemplate.html'
		};
	});

})(angular, Riff, jQuery);

(function(ng, app) {

	'use strict';

	app.filter('friendlyUrl', function() {
		return function(str) {

			// convert spaces to '-'
			str = str.replace(/ /g, '-');

			// Make lowercase
			str = str.toLowerCase();

			// Remove characters that are not alphanumeric or a '-'
			str = str.replace(/[^a-z0-9-]/g, '');

			// Combine multiple dashes into one
			str = str.replace(/[-]+/g, '-');

			return str;
		};
	});

})(angular, Riff, jQuery);


