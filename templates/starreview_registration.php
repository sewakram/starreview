<?php
/**
 *@package StarReview
 */

class StarPRSregistration
{
	const NONCE_VALUE = 'prs_star_register';
    const NONCE_FIELD = 'starregister_nonce';
    protected $errors = array();
    protected $data = array();
	
	function __construct()
	{
		add_shortcode( 'starreview_form', array($this, 'prs_register_shortcode') );
		add_shortcode( 'starreview_activate_user', array($this, 'prs_activate_user') );
		add_action( 'template_redirect',  array( $this, 'prs_handle_form' ) );
	}

	public function prs_activate_user() {
		// echo fdfdf$_GET['user_id'];
		
		global $wpdb;
		if(isset($_GET['key']) && isset($_GET['user'])){
			$singledata = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}users WHERE ID ='".$_GET['user']."' AND user_activation_key='".$_GET['key']."'",OBJECT);
			$updatedata=$wpdb->update( "{$wpdb->prefix}users", array( 'user_status' => 1,'user_activation_key'=>''), array( 'ID' => $singledata->ID ) );
			// echo "<pre>";print_r($updatedata);die();
			if($updatedata){
				?>
				<h3>Your account has been activated!</h3>
				<p><strong>Please note:</strong>Your Review will enter the assessment queue and will go live within 7 business days once approved</p>
				<?php
			}else{
				echo "<h3>Already, your account has been activated!</h3>";
			}

		}

	}
	public function prs_register_shortcode() {
        if ( is_user_logged_in() )
        {
        	if(is_page( 'user_registration' ))
        	{
        		return sprintf( '<p>Please <a href="%s">Logout</a> for new registration.</p>', esc_url( wp_logout() ) );
        	}
        }
        elseif ( $this->isFormSuccess() )
        {
        	if(isset($_COOKIE['email'])){ $email = $_COOKIE['email']; }else{ wp_redirect(site_url()); }
            return '<h1>You’re almost done! Please check your inbox.</h1>
<p>To activate your account and validate your posts, please click the confirmation link in the email that was sent to <span class="text-colored-blue"><b>'.$email.'</b></span>.</p>
<p><b>Please Note</b>: Your Reviews and Comments cannot be published on consumerthought if your email isn’t verified.</p>
<h3>Not seeing the email?</h3>
<ul>
<li><span class="text-fail"><b>Check in your Spam folder.</b></span> Occasionally our first email to you can be filtered into Spam.</li>
<li>If our email did end up in you Spam/Junk folder, we recommend marking it “It’s not spam” to ensure the delivery of all future emails.</li>
<li>Or simply write us at <span class="text-fail"><b>kyzooma@consumerthought.com</b></span> with a subject line “Activate Account” and we\'ll get this taken care of ASAP!</li>
</ul>';
   //      	<div class="notify"><span class="symbol icon-info"></span> A kind of a notice box !</div> 
			// <div class="notify notify-red"><span class="symbol icon-error"></span> Error message</div> 
			// <div class="notify notify-green"><span class="symbol icon-tick"></span> A positive/success/completion message</div> 
			// <div class="notify notify-yellow"><span class="symbol icon-excl"></span> A warning message</div>
        }
        else
            return $this->register_form();
    }

    public function isFormSuccess()
    {
    	return filter_input( INPUT_GET, 'success' ) === 'true';
    }

    public function prs_handle_form()
    { 
    	if ( ! $this->prs_isFormSubmitted() )
    		return false;

    	$data = filter_input_array( INPUT_POST, array(
					    'email' => FILTER_DEFAULT,
					    'password' => FILTER_DEFAULT
    			)
    	);

    	$data = wp_unslash( $data );
        $data = array_map( 'trim', $data );
      	$data['email'] = sanitize_email( $data['email'] );
      	$data['password'] = $data['password'];
      
      	$this->data = $data;
      	global $wpdb;

      	if ( ! $this->isNonceValid() )
            $this->errors[] = 'Security check failed, please try again.';

      
        if ( ! $_POST['email'] )
            $this->errors['email'] = 'Please enter a email.';
        else
        	if (!is_email($data['email']) )
            $this->errors['email'] = 'Please enter a valid email address.';

         if(email_exists( $_POST['email'] ))
        	$this->errors['email'] = 'Email allready exists database!';


         if(empty($data['password']))
        	$this->errors['password'] = 'Please enter a password.';

        if(!$this->errors)
        {
        	global $wpdb;
        	 session_start();
        	 $comment = get_comment($_SESSION['comment_waiting']);
        	 $user_id = username_exists( $comment->comment_author );
			if ( $user_id )
			{
				$username = $comment->comment_author.''.wp_rand( 0, 99 );
			}
			else
			{
				$username = $comment->comment_author;
			}

	    	$user_id = wp_create_user( $username, $data['password'], $data['email'] );
	    	if($user_id)
	    	{

	    		update_usermeta( $user_id, 'first_name', $comment->comment_author );
    			update_usermeta( $user_id, 'user_location', get_comment_meta( $_SESSION['comment_waiting'], 'location', 'single' ) );
				 $code = sha1( $user_id . time() );
				 $activation_link = add_query_arg( array( 'key' => $code, 'user' => $user_id ), site_url('verify/'));    
				$headers = "MIME-Version: 1.0" . "\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\n";
				$conform=array();
				$conform['cnf_user']=$comment->comment_author;
				$conform['post_copy_year']=date("Y");
				$conform['post_sitename']=get_bloginfo( 'name' );
				$conform['post_siteemail']=get_bloginfo( 'admin_email' );
				$conform['verify_link']=$activation_link;
				$conformContent = file_get_contents(dirname(__FILE__).'/review_email_verify.html');
				
				foreach ($conform as $key => $value) {
				$conformContent=str_replace($key,$value,$conformContent);
				}
				// var_export($conformContent);exit;
				wp_mail( $data['email'], "Welcome to Consumerthought",$conformContent,$headers );
	    		
	    		$commentarr = array();
				$commentarr['comment_ID'] = $_SESSION['comment_waiting'];
				$commentarr['user_id'] = $user_id;
				$commentarr['comment_author_email'] = $data['email'];
				setcookie('email',$data['email']);
				wp_update_comment( $commentarr );
				unset($_SESSION['comment_waiting']);
		        global $wpdb;    
		        $wpdb->update( 
		            'wp_users',  
		                array( 'user_activation_key' => $code,     ),       
		                array( 'ID' =>    $user_id ),     
		                array( '%s',         )
		            );

		        
				wp_redirect( add_query_arg( 'success', 'true' ) );
                exit;
	    	}
	    	else
	    	{
	    		$this->errors['error'] = 'Whoops, please try again.';
	    	}
        }
    	
    }

    public function prs_getpname()
    {
 		$args = implode(',', $_POST['p_category']);
 		$option = '';
 		for ($i=0; $i < count($_POST['p_category']); $i++) { 
 			$prs_query = get_posts( array( 'category' => $_POST['p_category'][$i] ) );
 			$category = get_category( $_POST['p_category'][$i] );
 			$option .= '<option value="" disabled="disabled" style="text-align:center;font-weight:bold;color:black">'.$category->name.'</option>';
 			foreach ($prs_query as $prs_fetch) {
 				$option .= '<option value="'.$prs_fetch->ID.'">'.$prs_fetch->post_title.'</option>';
 			}
 		}
 		if($option != '')
 		{
 			echo json_encode( array( 'status' => 'success', 'p_name' => $option) );
 		}
    	else
    	{
    		echo json_encode( array( 'status' => 'unsuccess', 'p_name' => 'bad') );
    	}
    	die();
    }
    public function prs_isFormSubmitted()
    {
    	return isset( $_POST['starregister_nonce'] );
    }
	public function register_form()
	{
		ob_start();
		session_start();
		if(!isset($_SESSION['comment_waiting']))
		{
			return wp_redirect(site_url());
			exit;
		}
		$comment = get_comment($_SESSION['comment_waiting']);
		// echo get_comment_meta( $_SESSION['comment_waiting'], 'location', 'single' );
		// echo "<pre>";
		// print_r($comment);
		if(isset($this->errors['error']))
		{
			echo '<div class="prs_notify prs_notify-red"><span class="prs_symbol prs_icon-error"></span> '.$this->errors['error'].'</div>';
		}
		?>
		<h1>Thank you for reviewing <?php echo get_the_title( $comment->comment_post_ID ); ?>!</h1>
		<h2>Please create an account to post this review.</h2>
		<p>To ensure that all reviews meet our guidelines, all submissions are reviewed prior to being
		published live, which can take up to 7 business days.</p>
		<?php echo do_shortcode( '[starreview_facebook/]' ); ?>
		<form action="" method="post" id="starrating-signup">
			<div class="replyform">
				<h2><?php echo esc_html__( 'Registration', 'starreview' ); ?></h2>
					<div class="row">
						<div class="form-label">
							<label><?php echo esc_html__( 'Email', 'starreview' ); ?></label>
						</div>	
						<div class="form-input">
					 		<div class="input-row-full"> 
					 			<input type="hidden" name="comment_id" value="<?=$_SESSION['comment_waiting']?>">
					 			<input type="email" required="required" name="email" value="<?php if ( isset( $comment->comment_author_email ) )
			                    echo esc_attr( $comment->comment_author_email );?>"/>
					 			<p class="small"><?php echo esc_html__( 'example@example.com', 'starreview' ); ?></p><span class="prs_server_valid"><?php if(isset($this->errors['email'])){ echo $this->errors['email']; } ?></span>
					 		</div>
						</div>
						<div class="clear"></div>
					</div>

					<div class="row">
						<div class="form-label">
							<label><?php echo esc_html__( 'Password', 'starreview' ); ?></label>
						</div>	
				
						<div class="form-input">
					 		<div class="input-row-full"> 
					 			<input type="password" required="required" name="password" maxlength="7" value=""/>
					 			<span class="prs_server_valid"><?php if(isset($this->errors['password'])){ echo $this->errors['password']; } ?></span>
					 		</div>
						</div>
						<div class="clear"></div>
					</div>
					
					
					
					<?php wp_nonce_field( self::NONCE_VALUE , self::NONCE_FIELD ) ?>
					<div class="row rowwe">
						<input type="submit" name="submitForm" value="Sign UP" class="submit-btn">
					</div>
			</div>
					<script type="text/javascript">
				        	var lights = document.getElementsByClassName("prs_valid");
							while (lights.length)
							    lights[0].className = lights[0].className.replace(/\bprs_valid\b/g, "");
				    </script>
		</form>
		<?php
		return ob_get_clean();
	}

	/**
     * Is the nonce field valid?
     *
     * @return bool
     */
    public function isNonceValid() {
        return isset( $_POST[ self::NONCE_FIELD ] ) && wp_verify_nonce( $_POST[ self::NONCE_FIELD ], self::NONCE_VALUE );
    }
}
new StarPRSregistration();