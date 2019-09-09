jQuery(function($) {


	////////////////////////////////////////=>comment form validation start
		$("#commentform").validate({
		rules: {
		rating: "required",
		author: "required",
		email: {
		  required: true,
		  email: true
		},
		comment: {
				required: true,
				minlength: 100
			},
		location: {
			required: true
		},
		review_title: {
				required: true,
				minlength: 30
			},
		bottom_line: "required",
		terms_condition: "required",

		},

		messages: {
		rating: "Please select your rating",
		author: "Please specify your name",
		comment:{
			required: "Please write a comment",
			minlength: $.validator.format("Please, at least {0} characters are necessary")
		},
		email: {
		  required: "We need your email address to contact you",
		  email: "Your email address must be in the format of name@domain.com"
		},
		location: {
			required: "Please specify your location"
		},
		review_title:{
			required: "Please specify review title",
			minlength: $.validator.format("Please, review title at least {0} characters are necessary")
		},
		bottom_line:{
			required:"Please select bottom line"
		},
		terms_condition:{
			required:"Please accept terms and condition"
		}

		},
		

		});
	////////////////////////////////////////=>comment form validation end
	////////////////////////////////////////=>login form validation start
		$("#loginform").validate({

			rules: {
			log: {
				required: true,
				email: true
			},
			pwd: "required"
			
			},
			messages: {
			log:{
			required: "Please enter your email",
			email: "Your email must be in the format of name@domain.com"
			},
			pwd:"Please enter password"

			}

		});
	////////////////////////////////////////=>login form validation end
	////////////////////////////////////////=>signup form validation start
	$("#starrating-signup").validate({
		rules: {
			email: {
				required: true,
				email: true
			},
			password: {
				required: true,
				minlength: 6
			}
		},
		messages: {
			email:{
			required: "Please enter your email",
			email: "Your email must be in the format of name@domain.com"
			},
			password:{
				required: "Please enter password",
				minlength: $.validator.format("Password must be at least {0} characters")
			}

			}
	});
	////////////////////////////////////////=>signup form validation end
	////////////////////////////////////////=>forget password form validation start
	$("#lostpasswordform").validate({
		rules: {
			user_login: {
				required: true,
				email: true
			}
		},
		messages: {
			user_login:{
			required: "Please enter your email",
			email: "Your email must be in the format of name@domain.com"
			}

			}
	});
	////////////////////////////////////////=>forget password form validation end
	////////////////////////////////////////=>recaptcha validation start
		$( '#comment_submit' ).click(function(){
		var $captcha = $( '#recaptcha' ),
		  response = grecaptcha.getResponse();

		if (response.length === 0) {
		$( '.msg-error').text( "reCAPTCHA is mandatory" );
		if( !$captcha.hasClass( "error" ) ){
		  $captcha.addClass( "error" );
		}
		} else {
		$( '.msg-error' ).text('');
		$captcha.removeClass( "error" );
		alert( 'reCAPTCHA marked' );
		}
		})
	////////////////////////////////////////=>recaptcha validation end
	
});

