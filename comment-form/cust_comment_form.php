<?php



/**



 *@package StarReview



 */







/**



 * custom comment form



 */



@ob_start();



class cust_comment_form



{



	



	public function register($value='')



	{
		

		add_filter( 'comments_template', array( $this, 'comments_template' ) );
		
		add_action( 'wp_ajax_helpfull', array($this, 'helpfull') );
		add_action( 'wp_ajax_helpfullflag', array($this, 'helpfullflag') );
		 
		add_action( 'wp_ajax_nopriv_helpfull', array($this, 'helpfull') );
		add_action( 'wp_ajax_nopriv_helpfullflag', array($this, 'helpfullflag') );


		add_action('comment_post',array($this,'pulse_alert'), 19, 2);



        //default comment custom field 



		add_filter( 'comment_form_default_fields', array($this, 'custom_fields'), 10 );



        



        add_filter( 'comment_form_fields', array($this, 'star_comment_textarea' ) );







        add_filter( 'comment_form_submit_button', array($this, 'star_comment_submit_button' ) );



        



	    //custom comment form top field



        add_action( 'comment_form_top' , array($this, 'top_star_comment_form' ) );



        







        //custom comment form after field



		add_action( 'comment_form_after_fields', array( $this, 'after_star_comment_form') );



		







		add_action( 'comment_form_logged_in_after', array( $this, 'after_star_comment_form') );







		add_action( 'comment_post', array($this, 'save_comment_post_data' ),120,1 );

			add_action( 'set_comment_cookies', function( $comment, $user ) {
			    setcookie( 'ta_comment_wait_approval', '1' );
			}, 10, 2 );

			add_action( 'init', function() {
			    if( $_COOKIE['ta_comment_wait_approval'] === '1' ) {
			    	unset( $_COOKIE['ta_comment_wait_approval'] );
					setcookie( 'ta_comment_wait_approval' , '', time() - ( 15 * 60 ) );
			        add_action( 'comment_form_before', function() {
			        	session_start();
			        	if(isset($_SESSION['commenterror']) && ($_SESSION['commenterror'] != ''))
			        	{
			        		echo "<p id='wait_approval' style='padding-top: 40px;color:#d93025'><strong>".$_SESSION['commenterror']."</strong></p>";
			        		unset($_SESSION['commenterror']);
			        	}
			        	else
			        	{
			        		echo "<p id='wait_approval' style='padding-top: 40px;color:#35ac19'><strong>Thanks for your comment. We appreciate your response.</strong></p>";
			        	}
			            
			        });
			    }
			});

			add_filter( 'comment_post_redirect', function( $location, $comment ) {



			    $location = get_permalink( $comment->comment_post_ID ) . '#wait_approval';



			    return $location;



		}, 10, 2 );


		add_filter( 'get_avatar_comment_types', array( $this, 'avatar_comment_types' ) );

		add_shortcode( 'user_registration', array($this, 'user_registration_shortcode') );

		add_shortcode( 'user_dashboard', array($this, 'user_dashboad_shortcode') );
		add_shortcode( 'reply-comment',  array($this, 'comment_reply_activity'));

		add_filter( 'login_redirect', array($this,'my_login_redirect'), 10, 3 );
		
	}

		public function comment_reply_activity()
	{
		// Hook into wp_enqueue_scripts
		add_action( 'comment_form_before', array($this, 'mytheme_enqueue_comment_reply' ));


		
		if(isset($_GET['post_id']) && isset($_GET['parent_id']) && isset($_GET['comment_id']))
		{
			wp_list_comments(  $args = array(),  $comments = $_GET['comment_id'] );

			comment_form(array(), $_GET['post_id']);
			echo '<script>
				setTimeout(function(){ jQuery(".comment-reply-link").trigger("click"); }, 1000);
				jQuery(".gpur-add-user-ratings-wrapper").remove(),jQuery(".gpur-comment-form-title").remove(),jQuery(".comment-form-attachment").remove(),jQuery(".comentlink").remove(),jQuery("#starrate").val("0");
			</script>';
		}
		
	}

	public function avatar_comment_types( $types ) {
			$types[] = 'review';
			return $types;
	}
		
	public static function StarLost_Password()
	{	
		$new_query = add_query_arg(array('perform' => 'lostpassword'), get_permalink());
	?>
			<div class="tabdivouter">
					<h3>Forgot Password</h3>
					<div class="tabdivinner-left" style="float:left; margin: 0px auto; width: 50%;">
						<div id="registration">
							<form method="post" action="<?php echo get_permalink($new_query); ?>" id="lostpasswordform" name="lostpasswordform">
								<p>
									<label for="user_login"><?php _e('E-mail:'); ?><br>
									<input type="email" size="30" value="" class="input" id="user_login" name="user_login" required="required"></label>
								</p>
								<input type="hidden" value="" name="redirect_to">
								<p class="submit" style="margin-top: 15px;">
								
								<input type="submit" value="Get New Password" class="button button-primary button-large" id="wp-submit" name="wp-submit">
								</p>
							</form>
						</div>
					</div>
			</div>
			
	<?php
	}
	
 
	public function user_dashboad_shortcode( ) {

	 if(!is_user_logged_in()){
	    wp_redirect( home_url('/user-registration') );
	 }
	 	require_once(plugin_dir_path( __FILE__ ).'star-user-dashboard.php');
	}
	public function my_login_redirect($redirect_to, $requested_redirect_to, $user) {
		if( $user->caps['subscriber']!=1) {
			return $redirect_to;
			exit;
		}
		$error_types = array_keys($user->errors);
		   if (is_wp_error($user)) {
		        //Login failed, find out why...
		        $error_types = array_keys($user->errors);
		      
		        $error_type = $error_types[0];
		        if($error_types[0])
		        {
		        	wp_redirect( site_url( 'user-registration' ). "?login=failed&reason=" . $error_type ); 
		        	exit;
		        }
		    }
		    else{

		    	if(  $user->user_status == 1 && $user->caps['subscriber']==1) {
		    		wp_redirect( site_url( 'dashboard' ) );
		    		exit;
		    	}
		    	else
		    	{
		    		return $redirect_to;
		    		exit;
		    	}
		    }
	
	}


		public function user_registration_shortcode() {


			$status = $_GET['reason'];

			switch( $status ) {
				case 'invalid_email':
					_e( '<strong>ERROR</strong>: Invalid email address.' );
					break;
				case 'invalid_username':
					_e( '<strong>ERROR</strong>: Invalid username.' );
					break;
				case 'empty_password':
					_e( '<strong>ERROR</strong>: The password field is empty.' );
					break;
				case 'incorrect_password':
					_e( '<strong>ERROR</strong>: The password you entered for the email address is incorrect. ','starreview' );
					break;
				case 'awaiting_activation':
					_e( '<strong>ERROR</strong>: You need to activate your account.' );
					break;
				case 'empty_username':
					_e( '<strong>ERROR</strong>: The username field is empty.' );
					break;
				case 'loggedout':
					_e( 'You are now logged out.' );
					break;
		    }

			if(!is_user_logged_in())
			{
				if(isset($_GET['perform']))
				{
					if($_GET['perform'] === 'lostpassword')
					{

							if(isset($_POST['user_login']))
							{
								
								$email	=	(isset($_REQUEST['user_login'])) ? $_REQUEST['user_login'] : ''; 
								if (empty($email) || !is_email($email)) {
									echo 'Not A Valid Email address';
								}else if ( !email_exists($email) ){
									echo 'Email address not exist';
								}
								else
								{

									$user = get_user_by('email', $email);
									$newpass	=	wp_generate_password();
									$name	=	$user->first_name .' '. $user->last_name;
									wp_set_password( $newpass, $user->ID );

									$blog_title = get_bloginfo('name'); 
									$admin_email = get_bloginfo('admin_email'); 
									add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
									$headers = 'From: ConsumerThought <'.$admin_email.'>' . "\r\n";
									$message	=
									"Hello $name,
									<p>We have reset your password here is your new password please check it</p>
									<p>======================================================================</p>
									<p>New Password : $newpass</p>
									<p>======================================================================</p>
									<p>For more details please contact to administrator</p>
									<p>Thanks & Regards<br/>$blog_title</p>
									<br/>";
									$sent	=	wp_mail($email, 'New Password', $message, $headers);

									if($sent){
										$_POST = array();
										session_start();
										$_SESSION['password_hint'] = $email;
										$msg = "password_send_success";
										wp_redirect(site_url('user-registration?msg='.$msg));
									}else{
										$_POST = array();
										session_start();
										$_SESSION['password_hint'] = $email;
										$msg =  "password_send_fail.";
										wp_redirect(site_url('user-registration?msg='.$msg));
										
									}
								}
											
							}

						$lostpassword = new cust_comment_form();
						$lostpassword::StarLost_Password();
						exit;
					}
				}
				$args = array();
				
				if(isset($_GET['msg']))
				{
					session_start();
					if(isset($_SESSION['password_hint']))
					{
						if($_GET['msg'] === 'password_send_success')
						{
							echo "New Password Sent on you ".$_SESSION['password_hint']." mail address.";
						}
						elseif ($_GET['msg'] === 'password_send_fail') {

						 	echo "Error while sending mail. Please try it later.";
						}
						unset($_SESSION['password_hint']);	
					}
				}
				add_filter(  'gettext',  'register_text'  );
				function register_text( $translating ) {
				     $translated = str_replace(  'Username or Email Address',  'Email Address',  $translating );
				    return $translated;
				}
				wp_login_form( $args );
				echo "<a href='".site_url('user-registration?perform=lostpassword')."'>Forget Password</a>";
				echo do_shortcode('[starreview_facebook/]');	

			}
			elseif( get_currentuserinfo()->caps['administrator'] == 1 )
			{
				header("Location:".admin_url());die();
				
			}
			else
			{
				header("Location:".site_url('dashboard/'));
			}
		}


	 public function comments_template( $template ) {

				

				return plugin_dir_path( __FILE__ ) . '/comments-template.php';

		}

	public function helpfull( ) {
		 global $wpdb;
			// var_export($_POST);//exit;

		 $chk=$wpdb->get_row("SELECT count(*) as count FROM {$wpdb->prefix}commentmeta WHERE comment_id ='".$_POST['cid']."' AND meta_key='IP' AND meta_value='".$_SERVER['REMOTE_ADDR']."'",OBJECT);
		 // print_r( $chk->count);exit;
		 if($chk->count==0){
		 	$wpdb->insert("{$wpdb->prefix}commentmeta",array( 'comment_id' => $_POST['cid'],'meta_key' => 'helpfull','meta_value' => $_POST['myvalue']),array('%d','%s','%s'));
		 $wpdb->insert("{$wpdb->prefix}commentmeta",array( 'comment_id' => $_POST['cid'],'meta_key' => 'IP','meta_value' => $_SERVER['REMOTE_ADDR']),array('%d','%s','%s'));
		 $total = $wpdb->get_row("SELECT count(*) as total FROM {$wpdb->prefix}commentmeta WHERE comment_id ='".$_POST['cid']."' AND meta_key='helpfull'",OBJECT);
		 $totalyes = $wpdb->get_row("SELECT count(*) as yes FROM {$wpdb->prefix}commentmeta WHERE comment_id ='".$_POST['cid']."' AND meta_key='helpfull' AND meta_value='y'",OBJECT);
			
      		echo json_encode(array($total,$totalyes)); die();
		 }
		 

		}


			public function helpfullflag( ) {
			 // echo gettype($_POST['myvalue']);die();
				
		 	global $wpdb;
			$myvalue= (gettype($_POST['myvalue'])=='array') ? json_encode($_POST['myvalue']) : $_POST['myvalue'];

			 // var_dump($myvalue);exit;
		 $chk=$wpdb->get_row("SELECT count(*) as count FROM {$wpdb->prefix}commentmeta WHERE comment_id ='".$_POST['cid']."' AND meta_key='IPflag' AND meta_value='".$_SERVER['REMOTE_ADDR']."'",OBJECT);
		 // print_r( $chk->count);exit;
		 $data = array();
		 if($chk->count==0){
		 	$wpdb->insert("{$wpdb->prefix}commentmeta",array( 'comment_id' => $_POST['cid'],'meta_key' => 'helpfullflag','meta_value' => $myvalue),array('%d','%s','%s'));
		 $wpdb->insert("{$wpdb->prefix}commentmeta",array( 'comment_id' => $_POST['cid'],'meta_key' => 'IPflag','meta_value' => $_SERVER['REMOTE_ADDR']),array('%d','%s','%s'));
		 // $total = $wpdb->get_row("SELECT count(*) as total FROM {$wpdb->prefix}commentmeta WHERE comment_id ='".$_POST['cid']."' AND meta_key='helpfullflag'",OBJECT);
		 // $totalyes = $wpdb->get_row("SELECT count(*) as yes FROM {$wpdb->prefix}commentmeta WHERE comment_id ='".$_POST['cid']."' AND meta_key='helpfull' AND meta_value='y'",OBJECT);
			
      		// echo json_encode(array($total,$totalyes)); die();
		  // echo 'done';die();
		 
		 $data['status'] = true;
		echo json_encode($data);
		die();
		 }else{
		 	

		 	$data['status'] = false;
		 	echo json_encode($data);die();
		 }
		 

		}

    



    public function custom_fields($fields)



    {



 



 		 $commenter = wp_get_current_commenter();



         $req = get_option( 'require_name_email' );



         $aria_req = ( $req ? " aria-required='true'" : ’ );







       $fields['author'] = '<p class="comment-form-author" style="display:none">'.



      '<label for="author">' . esc_html__( '*Name', 'starreview' ) . '</label>'.



      '<input id="author" name="author" type="text" value="'. esc_attr( $commenter['comment_author'] ) .



      '" size="30" placeholder="'.esc_attr__( 'Please Tell us your name', 'starreview' ).'" tabindex="1"' . $aria_req . ' /></p>';







	    $fields['email'] = '<p style="display:none" class="comment-form-email"  >'.



		  '<label for="email">'. esc_html__( '*Email', 'starreview' ) .'</label>'.



		  '<input id="email" type="email" name="email" value="'. esc_attr( $commenter['comment_author_email'] ) .'" placeholder="' . esc_attr__( 'Your Email never be shared', 'starreview' ) . '" '. $aria_req .'/></p>';







          



          unset($fields['url']);







          unset($fields['cookies']);







		  return $fields;



    }







    public function star_comment_textarea($fields)



    {



    	$comment_field = $fields['comment'];







    	unset($fields['comment']);



    	



         return $fields;



    }



    



    public function star_comment_submit_button()



    {


    	// if(get_current_user_id()){
    	 $comment_button = '<p class="form-submit"><input name="submit" type="submit" id="comment_submit" class="submit" value="Post Your Review"></p>';
    	// }
		// <input type="hidden" name="comment_post_ID" value="5" id="comment_post_ID">



		// 	<input type="hidden" name="comment_parent" id="comment_parent" value="0">





		return $comment_button;



	}







	public function top_star_comment_form()



	{



		/* echo '<p class="comment-form-rating">'.



		  '<label for="rating">'. esc_html__( '*Your Rating', 'starreview' ) . '</label>



		  <span class="commentratingbox">';



		  $totalrate = get_option('total_rating');



		    for( $i=1; $i <= $totalrate; $i++ )



		    echo '<span class="commentrating"><input type="radio" name="rating" id="rating" value="'. $i .'"/>'. $i .'</span>';



		  echo'</span></p>';*/



        



        echo '<div class="ratetop"><span class="rwp-ratings-form-label">Write a Review</span>';



        echo '<fieldset class="therating">



        <span class="label newlabel"><span class="tooltip">i<span class="tooltiptext">Please rate your experience with the following product. Note: Add a rating in between (1-5)</span></span> Your Rating : </span>



        <input type="radio" name="rating" id="4_stars"  value="5"/>



        <label class="stars default" msg="Great Product! I Recommend it!" title="5" for="4_stars" id="5stars" data-rating="5"  onclick="rateme(this)">4 stars</label>



        <input type="radio" name="rating" id="3_stars"  value="4"/>



        <label class="stars default" msg="Pretty Good..." for="3_stars" title="4" id="4stars" data-rating="4"   onclick="rateme(this)">3 stars</label>



        <input type="radio" name="rating" id="2_stars"  value="3"/>



        <label class="stars default" msg="I`d Say it`s Average." title="3" for="2_stars" id="3stars" data-rating="3"  onclick="rateme(this)">2 stars</label>



        <input type="radio" name="rating" id="1_stars"  value="2"/>



        <label class="stars default" msg="Not what I was Hoping For" title="2" for="1_stars" id="2stars" data-rating="2"  onclick="rateme(this)">1 star</label>



        <input type="radio" name="rating" id="0_stars" value="1"/>



        <label class="stars default" msg="Really Bad Experience..." title="1" for="0_stars" id="1stars" data-rating="1"  onclick="rateme(this)">0 star</label>



        </fieldset><p class="big-rating-description">Please Rate Your Experience</p><input type="hidden" id="starchange" value=""/></div>';

        



        



        



        echo '<script>
        		jQuery(function($) 
						{	
						
						var getUrlParameter = function getUrlParameter(sParam) {
						var sPageURL = window.location.search.substring(1),
						sURLVariables = sPageURL.split("&"),
						sParameterName,
						i;

						for (i = 0; i < sURLVariables.length; i++) {
						sParameterName = sURLVariables[i].split("=");

						if (sParameterName[0] === sParam) {
						return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
						}
						}
						};

						if(getUrlParameter("stars") && getUrlParameter("stars")!="undefined"){
							$("#"+getUrlParameter("stars")+"stars").trigger("click");
						}

							

						$("label.stars").click(function(){
							console.log("sewak"+$(this).data("rating"));
							
							if (history.pushState) {
							var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?stars="+$(this).data("rating");
							window.history.pushState({path:newurl},"",newurl);
							}

							$(".areanew").show();
							$(".comment-form-comment").show();
							$(".comment-form-review-title").show();  
							$(".comment-form-product-file").show();  
							$(".prodprchs").show();  
						});

						$(".comment-form-comment").keyup(function(){
							console.log("sewak");
							$("#social_div").show();
						});

						$(".withemail").click(function(){
							$("p.comment-form-author").toggle("show");
							$("p.comment-form-email").toggle("show");
						 
						});

						});

				function rateme(thisref) {



					var current=$(thisref).data("rating");


				 jQuery("#starchange").val(current);
				console.log(current);



					// $("label").addClass("default");







					if(current <3)



					{



					if(current==1){



					$("#1stars").addClass("red").removeClass("default").removeClass("green").removeClass("yellow");



					$("#2stars").addClass("default").removeClass("red").removeClass("green").removeClass("yellow");   



					$("#3stars").addClass("default").removeClass("red").removeClass("green").removeClass("yellow");   



					$("#4stars").addClass("default").removeClass("red").removeClass("green").removeClass("yellow");   



					$("#5stars").addClass("default").removeClass("red").removeClass("green").removeClass("yellow");   



					}	



					if(current==2){



					$("#1stars").addClass("red").removeClass("default").removeClass("green").removeClass("yellow");   



					$("#2stars").addClass("red").removeClass("default").removeClass("green").removeClass("yellow");   



					$("#3stars").addClass("default").removeClass("red").removeClass("green").removeClass("yellow");   



					$("#4stars").addClass("default").removeClass("red").removeClass("green").removeClass("yellow");   



					$("#5stars").addClass("default").removeClass("red").removeClass("green").removeClass("yellow");   



					}



					     



					}



					else   if((current>2) && (current <= 4))



					{



					$("#1stars").addClass("yellow").removeClass("default").removeClass("red").removeClass("green");   



					$("#2stars").addClass("yellow").removeClass("default").removeClass("red").removeClass("green");	



					if(current==3){



						$("#1stars").addClass("yellow").removeClass("default").removeClass("red").removeClass("green");   



						$("#2stars").addClass("yellow").removeClass("default").removeClass("red").removeClass("green");



						$("#3stars").addClass("yellow").removeClass("default").removeClass("red").removeClass("green");



						$("#4stars").addClass("default").removeClass("green").removeClass("red").removeClass("yellow");



						$("#5stars").addClass("default").removeClass("green").removeClass("red").removeClass("yellow");



					}



					if(current==4){







					$("#1stars").addClass("yellow").removeClass("default").removeClass("red").removeClass("green");



					$("#2stars").addClass("yellow").removeClass("default").removeClass("red").removeClass("green");



					$("#3stars").addClass("yellow").removeClass("default").removeClass("red").removeClass("green");



					$("#4stars").addClass("yellow").removeClass("default").removeClass("red").removeClass("green");



					$("#5stars").addClass("default").removeClass("yellow").removeClass("red").removeClass("green");	



					}



		



					}



					else   if(current>4)



					{



					$("#0stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");   



					$("#1stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");   



					$("#2stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");	



					$("#3stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");	



					$("#4stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");



					if(current==5){



					$("#0stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");   



					$("#1stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");   



					$("#2stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");	



					$("#3stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");	



					$("#4stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");



					$("#5stars").addClass("green").removeClass("default").removeClass("red").removeClass("yellow");



					}



					



					}



				}



			</script>';



		  // echo '<p class="comment-form-location" >'.



		  // '<label for="location">'. esc_html__( '*Location', 'starreview' ) .'</label>'.



		  // '<input id="location" type="text" name="location" value="" placeholder="' . esc_attr__( 'Example:Delhi, IND', 'starreview' ) . '"/></p>';







		   







		   echo '<div class="areanew" style="display:none"><p class="comment-form-comment" style="display:none"><label for="comment">'. esc_html__( '*Your Review', 'starreview' ) .'</label><span class="tooltip"> ?<span class="tooltiptext">Your review should be atleast 100 characters</span></span> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" placeholder="' . esc_attr__( 'Kindly share your honest experience, and help others make better choices.', 'starreview' ) . '" required="required"></textarea></p></div>';


		   echo '<div class="areanew" style="display:none"><p class="comment-form-review-title" style="display:none" >'.



		  '<label for="review-title">'. esc_html__( '*Review Title', 'starreview' ) .'</label><span class="tooltip"> ?<span class="tooltiptext">Review title should be atleast 30 characters</span></span>'.



		  '<input id="review_title" type="text" name="review_title" value="" placeholder="' . esc_attr__( 'Please add a suitable review title.', 'starreview' ) . '"/></p></div>';
		   echo '<div class="areanew" style="display:none"><p class="comment-form-product-file" style="display:none">
				      <label for="review-product-file">'. esc_html__( 'Purchased Bill', 'starreview' ) .'</label>
				      <div class="prodprchs" style="display:none"><span class="uplodbill">Upload the Product Purchased Bill</span><input id="product_file" type="file" name="product_file" value="" placeholder="Optional and will remain private, helps validate review"/><a class="aqcirle" href="#openModal">Explanation<span>?</span></a>
				      </div>
		     	 </p></div>
		      <div class="clear"></div>';

		  if(!is_user_logged_in()){
		  	echo '<div id="social_div" style="display:none">'.do_shortcode('[starreview_facebook/]').'
		   <a class="btn btn-light btn-small withemail"  href="JavaScript:Void(0);" rel="nofollow">Continue with email</a>
		   </div>';
		  }
		   


		  







		  //   echo '<p class="comment-form-bottom-line" >'.



		  // '<label for="review-bottom-line">'. esc_html__( '*Bottom Line', 'starreview' ) .'</label>'.



		  // '<input id="bottom-line-yes" type="radio" name="bottom_line" value="y" />'.esc_html__( 'Yes, I would recommend this to a friend', 'starreview' ).'</p><p><input id="bottom-line-no" type="radio" name="bottom_line" value="n" />'.esc_html__('No, I would not recommend this to a friend', 'starreview').'</p>';


	}




	public function after_star_comment_form()
	{


		echo '<style>

		.modalDialog {
    position: fixed;
    font-family: Arial, Helvetica, sans-serif;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.8);
    z-index: 99999;
    opacity:0;
    -webkit-transition: opacity 400ms ease-in;
    -moz-transition: opacity 400ms ease-in;
    transition: opacity 400ms ease-in;
    pointer-events: none;
}
.modalDialog:target {
    opacity:1;
    pointer-events: auto;
}
#openModal a.close {
	text-decoration: none;
	color: #fff;
}
.modalDialog > div {
    max-width: 630px;
    position: relative;
    margin: 10% auto;
    padding: 5px 20px 13px 20px;
    border-radius: 10px;
    background: #fff;
}
.close {
    background: #606061;
    color: #FFFFFF;
    line-height: 25px;
    position: absolute;
    right: -12px;
    text-align: center;
    top: -10px;
    width: 24px;
    text-decoration: none;
    font-weight: bold;
    -webkit-border-radius: 12px;
    -moz-border-radius: 12px;
    border-radius: 12px;
    -moz-box-shadow: 1px 1px 3px #000;
    -webkit-box-shadow: 1px 1px 3px #000;
    box-shadow: 1px 1px 3px #000;
}
.close:hover {
    background: #606061;
}	
		</style>';
 		echo'

<div id="openModal" class="modalDialog">
    <div>	<a href="#close" title="Close" class="close">X</a>

        	<h2>What is a "Verified Reviewer" Review?</h2>

<p>Consumer Health Digest uses the "Verified" badge to let our readers know that the review in question comes from an authenticated reviewer that has taken steps to prove that they are indeed real users of the product and ones that have been known to give valuable insight over a broad range of products. It’s how we let our patrons know that our reviews can be trusted and that they will help them make the right purchasing decisions.</p>

<p>The "Verified Reviewer" authentication is just one more way that we strive to bring our readers the most accurate and reliable information.</p>

<p>When reviews don’t have the "Verified Reviewer" label, it means that the reviewer has not taken the verification steps required for this distinctive badge. While some reviews may not carry the "Verified Reviewer" badge, it does not necessarily mean the reviewer is not giving a genuine assessment of the product and has no experience with the company. It simply means that we could not confirm a specific purchase.</p>

<h2>How can I make my review "Verified"?</h2>

<p>If we are unable to verify you automatically, you can email us the supporting documents that prove you have purchased the item in question through our Contact Us page. These documents can include invoices, receipts, delivery notes or any relevant proof of purchase. We will look into all submitted documentation for authenticity and verify users accordingly.</p>
        
    </div>
</div>';


 			
		echo '<div class="areanew1"><p class="comment-form-terms-condition" >'.



		  '<input id="terms-condition" type="checkbox" name="terms_condition" value="y" /><span>Privacy Policy</span><br>
		  '.esc_html__( 'Submitting this review means that you agree to our ', 'starreview' ).'<a href="'.site_url('review-guidelines').'">'.esc_html__( ' Review Guidelines', 'starreview' ).'</a> , confirming that you are a verified customer who has purchased the product and may have used the merchandise or experienced the service, and providing only a real interaction and experience without ulterior motives or has an affiliate or business with the company in any way. By ticking this box and submitting this review, you also accept that submitting fake reviews is a violation of '.get_bloginfo( 'name' ).'’s <a href="'.site_url('terms-and-conditions').'">'.esc_html__( ' Terms of Use', 'starreview' ).'</a>'.esc_html__( ' and such conduct will not be tolerated.', 'starreview' ).'
		  </p>';



		 

		  echo ('<style type="text/css">
		.msg-error {
		color: #f00;
		}
		.g-recaptcha.error {
		border: solid 2px #f00;
		padding: .2em;
		width: 19em;
		}
    	</style>');

		  
		  echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script><span class="msg-error error"></span><br><div id="recaptcha" class="g-recaptcha" data-sitekey="6LeS9KwUAAAAABuxlDV8k0P-AJPV--3INNHiKvLK"></div></div>';
		 


		  //echo '<input type="hidden" name="post_id" value="'.get_the_ID().'"/>';



		  //echo '<p>'.do_shortcode( '[anr-captcha]' ).'</p>';

        


    }





    public function pulse_alert($comment_ID, $approved) {
    	/////////////////////////////////////

  


	global $pagenow;	



	$comment = get_comment( $comment_ID );

	// echo "<pre>";print_r($comment);exit();

    $comment_author_email=$comment->comment_author_email;



	$post = get_post( $comment->comment_post_ID );



	$user = get_user_by('id', $post->post_author);



     // echo "<pre>";print_r($comment);die();



		if($comment->notification==1){



		if($comment_ID){



				$headers = "MIME-Version: 1.0" . "\n";



				$headers .= "Content-type:text/html;charset=UTF-8" . "\n";



				// $headers .= 'Cc:'.get_bloginfo( 'admin_email' ). "\r\n";



			if ( $approved == 1 ) {



				$variables = array();



				$variables['admin_user'] = $user->display_name;



				$variables['post_copy_year']=date("Y");



				$variables['post_sitename']=get_bloginfo( 'name' );



				$variables['post_siteemail']=get_bloginfo( 'admin_email' );



				$variables['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url('?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;



			$htmlContent = file_get_contents(dirname(__FILE__).'/emailapproveadmin.html');



			foreach($variables as $x => $value) {



				$htmlContent=str_replace($x,$value,$htmlContent);



				}



			mail( $user->user_email, "Comment created and Approved ", $htmlContent,$headers );







		}else{



					$emailcommentpost=array();



					$emailcommentpost['post_user']=$comment->comment_author;



					$emailcommentpost['post_copy_year']=date("Y");



					$emailcommentpost['post_sitename']=get_bloginfo( 'name' );

					$emailcommentpost['post_title']=get_the_title($comment->comment_ID);



					$emailcommentpost['post_siteemail']=get_bloginfo( 'admin_email' );



					$emailcommentpost['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url( '?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;



				$commentpostContent = file_get_contents(dirname(__FILE__).'/emailcommentpost.html');



				foreach($emailcommentpost as $x => $value) {



					$commentpostContent=str_replace($x,$value,$commentpostContent);



					}



				mail( $comment_author_email, "Comment has been posted successfull", $commentpostContent,$headers);



				$parentcomment = get_comment( $comment->comment_parent);



					if($parentcomment->notification==1){



				if($comment->comment_parent!=0){



							$author = get_comment_author_email( $comment->comment_parent );



							$emailcommentreply=array();



							$emailcommentreply['replay_user']=get_comment_author($comment->comment_parent);



							$emailcommentreply['post_copy_year']=date("Y");



							$emailcommentreply['post_sitename']=get_bloginfo( 'name' );



							$emailcommentreply['post_siteemail']=get_bloginfo( 'admin_email' );



							$emailcommentreply['post_parentmsg']=$parentcomment->comment_content;



							$emailcommentreply['post_msg']=$comment->comment_content;



							$emailcommentreply['post_replay_link']=site_url('replyemail/?').'comment_id='.$comment->comment_ID.'&parent_id='.$comment->comment_parent.'&post_id='.$comment->comment_post_ID;



							$emailcommentreply['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;



						$commentreplyContent = file_get_contents(dirname(__FILE__).'/emailcommentreply.html');

     


						foreach ($emailcommentreply as $key => $value) {



								$commentreplyContent=str_replace($key,$value,$commentreplyContent);



							}


							// echo "<pre>";print_r($commentreplyContent);die();
						mail( $author, 'Reply on comment', $commentreplyContent,$headers );



						// mail(get_bloginfo( 'admin_email' ), "Reply on comment", $commentreplyContent,$headers );







				}else{



							$emailparentapprove=array();



							$emailparentapprove['parent_user']=$user->display_name;



							$emailparentapprove['post_copy_year']=date("Y");



							$emailparentapprove['post_sitename']=get_bloginfo( 'name' );



							$emailparentapprove['post_siteemail']=get_bloginfo( 'admin_email' );



							$emailparentapprove['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url( '?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;



						$parentapproveContent = file_get_contents(dirname(__FILE__).'/emailparentapprove.html');



						foreach ($emailparentapprove as $key => $value) {



							$parentapproveContent=str_replace($key,$value,$parentapproveContent);



							}



						mail( $user->user_email, "New comment posted", $parentapproveContent,$headers );	



				}



			}



				



			}







			}



	}



			



	
//////////////////////////////
    }



    public function save_comment_post_data($comment_id)



    {
    	
    	
    	
    	// echo "<pre>";print_r($_POST);die();



    	// echo "<pre>";



    	// print_r($_FILES['product_file']);die();

    		//////////////////////////////////////
    	$captcha;
        
        if(isset($_POST['g-recaptcha-response'])){
          $captcha=$_POST['g-recaptcha-response'];
        }
        if(!$captcha){
       
        	echo ("<script LANGUAGE='JavaScript'>
    window.alert('Please check the the captcha form');
    window.location.href='".wp_get_referer()."';

    </script>");
          exit;
        }
        $secretKey = "6LeS9KwUAAAAAKpMwabxEBUyOnxWuKXZDjjubq37";
        $ip = $_SERVER['REMOTE_ADDR'];
        // post request to server
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
        $response = file_get_contents($url);
        $responseKeys = json_decode($response,true);
        // should return JSON with success as true
        if($responseKeys["success"]) {
                echo '<h2>Thanks for posting comment</h2>';
        } else {
                echo '<h2>You are spammer ! Get the @$%K out</h2>';
        }
    	//////////////////////////////////////

    		if(isset($_FILES['product_file']) && @$_FILES['product_file']['name'])



	    	{



	    		$validtypes = array(



					"image/jpeg" => true,



					"image/pjpeg" => true,



					"image/gif" => true,



					"image/png" => true,



					"image/x-png" => true



				);



				$validextensions = array('jpeg', 'jpg', 'gif', 'png');







				if( !$_FILES['product_file']['size'] ){



					$error = sprintf(__("The file &ldquo;%s&rdquo; was not uploaded. Did you provide the correct filename?", 'product-file'), $_FILES['product_file']['name']);



				}



				else if( !preg_match("/\.(" . join('|', $validextensions) . ")$/i", $_FILES['product_file']['name']) ){



					$error = sprintf(__("The file extension &ldquo;%s&rdquo; is not allowed. Must be one of: %s.", 'product-file'), preg_replace('/.*\./', '', $_FILES['product_file']['name']), join(', ', $validextensions));



				}



				else if( @!$validtypes[$_FILES['product_file']['type']] ){



					$error = sprintf(__("The uploaded file type &ldquo;%s&rdquo; is not allowed.", 'product-file'), $_FILES['product_file']['type']);



				}else{







					$tmppath = $_FILES['product_file']['tmp_name'];



				    $error = '';



					$imageinfo = null;



					$thumbinfo = null;



					$upload_dir = wp_upload_dir();



					$dir = trailingslashit($upload_dir['basedir']) . 'commentProductBill';



					if(!file_exists($dir))



					{



						mkdir($dir, 0777);



					}



					$imagefile = rand().'.'.preg_replace('{^.+?\.(?=\w+$)}', '', strtolower($_FILES['product_file']['name']));



					 $imagepath = $dir . '/' . $imagefile;



					 if(file_exists($imagepath))



					 {



					 	$imagepath = $dir . '/' .wp_rand( 1, 9 ).''.$imagefile;



					 }



					//  echo $tmppath.'<br/>';



					// echo $imagepath;die();



					if(!move_uploaded_file($tmppath, $imagepath)){



						$error = sprintf(__("Unable to upload your product file at: %s", 'product-file'), $_FILES['product_file']['name']);



					}



					add_comment_meta( $comment_id, 'product_file', $imagefile );



				}



				



				if(!empty($error))



				{



					wp_delete_comment( $comment_id );



					session_start();



					$_SESSION['commenterror'] = $error;



				}



	    	}







        	if(isset($_POST['rating']) && ($_POST['rating']) != '')



	    	{



	    		$rating = wp_filter_nohtml_kses( $_POST['rating'] );



	    		add_comment_meta( $comment_id, 'rating', $rating );



	    	}







	    	// if(isset($_POST['location']) && ($_POST['location']) != '')



	    	// {



	    	// 	$location = wp_filter_nohtml_kses( $_POST['location'] );



	    	// 	add_comment_meta( $comment_id, 'location', $location );



	    	// }







	    	if(isset($_POST['review_title']) && ($_POST['review_title']) != '')



	    	{



	    		$reviewtitle = wp_filter_nohtml_kses( $_POST['review_title'] );



	    		add_comment_meta( $comment_id, 'review_title', $reviewtitle );



	    	}







	    	// if(isset($_POST['order_number']) && ($_POST['order_number']) != '')



	    	// {



	    	// 	$order_number = wp_filter_nohtml_kses( $_POST['order_number'] );



	    	// 	add_comment_meta( $comment_id, 'order_number', $order_number );



	    	// }







	    	// if(isset($_POST['bottom_line']) && ($_POST['bottom_line']) != '')



	    	// {



	    	// 	$bottom_line = wp_filter_nohtml_kses( $_POST['bottom_line'] );



	    	// 	add_comment_meta( $comment_id, 'bottom_line', $bottom_line );



	    	// }











	    	if(isset($_POST['terms_condition']) && ($_POST['terms_condition']) != '')



	    	{



	    		$terms_condition = wp_filter_nohtml_kses( $_POST['terms_condition'] );



	    		add_comment_meta( $comment_id, 'terms_condition', $terms_condition );



	    	}


	     if(!is_user_logged_in())
		 {
		 	// mail( 'centsoft13@gmail.com', "New comment posted", $parentapproveContent,$headers );
		 	
		 	// //start comment

				// $comment = get_comment( $comment_id );

				// // 

				// $comment_author_email=$comment->comment_author_email;

				// // 

				// $post = get_post( $comment->comment_post_ID );



				// $user = get_user_by('id', $post->post_author);



				// // echo "<pre>";print_r($comment);die();



				// if($comment->notification==1){



				// if($comment_id){



				// $headers = "MIME-Version: 1.0" . "\n";



				// $headers .= "Content-type:text/html;charset=UTF-8" . "\n";



				// // $headers .= 'Cc:'.get_bloginfo( 'admin_email' ). "\r\n";

				// $emailcommentpost=array();



				// 	$emailcommentpost['post_user']=$comment->comment_author;



				// 	$emailcommentpost['post_copy_year']=date("Y");



				// 	$emailcommentpost['post_sitename']=get_bloginfo( 'name' );

				// 	$emailcommentpost['post_title']=get_the_title($comment->comment_ID);



				// 	$emailcommentpost['post_siteemail']=get_bloginfo( 'admin_email' );



				// 	$emailcommentpost['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url( '?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;

				

				// $commentpostContent = file_get_contents(dirname(__FILE__).'/emailcommentpost.html');



				// foreach($emailcommentpost as $x => $value) {



				// 	$commentpostContent=str_replace($x,$value,$commentpostContent);



				// 	}



				// mail( $comment_author_email, "Comment has been posted successfull", $commentpostContent,$headers);

				// }



				// }
		 	// //end comment
		 	if(!email_exists( $_POST['email'] ))
		 	{
		 		session_start();
		 		$_SESSION['comment_waiting'] = $comment_id;
			  	wp_redirect('write-review');
		      	exit;
		 	}
		 	else
		 	{
		 		
		 		session_start();
		 		$_SESSION['comment_waiting'] = $comment_id;
			  	wp_redirect('user-registration');
		      	exit;
		 	}
			 	
		 }








		    



	}







	function validation_comment_form($post)



	{



		$error = array();







		if($post['rating'] == '' || $post['first_name'] == '' || $post['last_name'] == '' || $post['email'] == '' || $post['location'] == '' || $post['review_title'] == '')



		{



			$error[] = 'All fields are required?';



		}







		return $error;



	}



    	



}
