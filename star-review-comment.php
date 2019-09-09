<?php

/**

 *@package StarReview

 */

/*

Plugin Name: Comment Star Review

Plugin URI: http://localhost/shop

Description: comment review plugin

Version: 1.0

Author: Pravin Shrikhande

Auhtor URI: http://localhost/shop

License: GPL

Text Domain: starreview

*/



defined('ABSPATH') or die('Hey you are silly user');

define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/* Google App Client Id */
define('CLIENT_ID', '897669795336-4ibpt89e18870eairqi6b4qm0met6d8h.apps.googleusercontent.com');

/* Google App Client Secret */
define('CLIENT_SECRET', '7lJUmXcSxEuctIVgXMhWcHqM');

/* Google App Redirect Url */
define('CLIENT_REDIRECT_URL', 'https://www.consumerthought.com/wp-admin/admin-ajax.php?action=starreview_prs_gmail');


/**

 * Star Review Class

 */

class StarReview

{

	

	function __construct()

	{

		 add_action( 'wp_enqueue_scripts', array($this, 'wp_starreview_enqueue_script') );

		 add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), array($this, 'starrating_setting_link'), '10', 1 );

		add_filter( 'authenticate', array($this, 'wpse32218_check_for_key'), 20, 3 );
		add_filter('wp_nav_menu_items', array($this, 'add_login_logout_link'), 10, 2);
		add_shortcode( 'unsubscribe_form', array($this, 'prs_unsubscribe_shortcode') );
		add_filter('user_row_actions', array($this,'active_deactive_users'), 11, 2);
		add_action( 'admin_enqueue_scripts', array($this, 'admin_starreview_enqueue_script') );
		add_action( 'plugins_loaded', array($this, 'check_current_user' ) );
		add_action('wp_ajax_cloadmore', array($this, 'misha_comments_loadmore_handler')); 
		add_action('wp_ajax_nopriv_cloadmore', array($this, 'misha_comments_loadmore_handler'));
		add_shortcode( 'share_social_screenshot', array($this, 'single_comment_share_socialmedia') );
	}

	public function single_comment_share_socialmedia()
	{
		wp_list_comments(  array( 'avatar_size' => 60 ),  $comments = $_GET['comment_id'] ); 
	}
	
	public function misha_comments_loadmore_handler(){
 
	// maybe it isn't the best way to declare global $post variable, but it is simple and works perfectly!
	global $post;
	$post = get_post( $_POST['post_id'] );
	setup_postdata( $post );
 	function prs_comments_ads_more( $comment, $args, $depth ) {
	     static $instance = 1;
	     $tag             = ( 'div' == $args['style'] ) ? 'div' : 'li';

	     echo "</{$tag}><!-- #comment-## -->\n";
	    
		     if ( 0 === $instance % 3 ) {
		         //echo "<{$tag} class=\"comment__ad\">YOUR AD HERE!</{$tag}>";
		     	   $postcat = get_the_category( get_the_ID() );
		           $first = count($postcat);

		           if(get_term_meta( $postcat[$first-1]->term_id, 'google_adds1', true))
		           {
		           	 echo get_term_meta( $postcat[$first-1]->term_id, 'google_adds1', true );
		           }
		     }
		
	     $instance++;
	}

	function format_comment($comment, $args, $depth) {
		include( plugin_dir_path( __FILE__ ) . 'comment-form/comment-template-list.php' );
	} 
	// actually we must copy the params from wp_list_comments() used in our theme
	wp_list_comments( array(
		'avatar_size' => 60,
		'page' => $_POST['cpage'], // current comment page
		'per_page' => get_option('comments_per_page'),
		'style'       => 'ol',
		'short_ping'  => true,
		'callback'=>'format_comment',
		'reply_text'  => function_exists( 'twentyseventeen_get_svg' ) ? twentyseventeen_get_svg( array( 'icon' => 'mail-reply' ) ) : '' . esc_html__( 'Reply', 'gpur' ),
		'end-callback' => 'prs_comments_ads_more',
	) );
	
		$current_cpage = get_query_var('cpage') ? get_query_var('cpage') : 1;
			    $comments_count = wp_count_comments( get_the_ID() );
				$pagescount = get_option('comments_per_page');
                $commentshow = $comments_count->approved - ($current_cpage * $pagescount);
    			$comment_args = array(
    			        'post_id'  => get_the_ID(),
    					'orderby' => 'comment_date_gmt',
    					'order' => 'ASC',
    					'offset' => $commentshow,
    					'status' => 'approve',
    			);
    			
    			$datacomment = get_comments( $comment_args );
    			
    			echo "<div id='loadhidecomment' style='display:none'><div id='commentchange'>";
    			
    			
    			$i = 1;
    			foreach($datacomment as $datahide)
    			{
    			    if($i <= $commentshow)
    			    {
    			        $output = '';
    			         $tot = get_comment_meta( $datahide->comment_ID, 'rating', true );
        							$output.= '<meta itemprop="worstRating" content = "1"/><br /><meta itemprop="ratingValue" content="'.$tot.'"/><br /> <meta itemprop="bestRating" content="5" />';
        				    
    			         echo "<div id='comment-id-".$datahide->comment_ID."' ><strong>".$datahide->comment_author." </strong><time style='font-size:14px' datetime='".$datahide->comment_date_gmt."'>".date('F d, Y, H:i a',strtotime($datahide->comment_date_gmt))."</time>".$output."<div class='gpur-comment-review-title'>".wp_strip_all_tags(get_comment_meta( $datahide->comment_ID, 'gpur_title', true ))."</div><div class='gpur-comment-review-text'>".wp_strip_all_tags($datahide->comment_content)."</div></div>";
    			    }
    			    else
    			    {
    			        break;
    			    }
    			   
    			    $i++;
    			}
    			echo "</div><div>";
		die; 
	} 
	
	public function check_current_user() {
	    // Your CODE with user data
	    $current_user = wp_get_current_user();

	    // Your CODE with user capability check
	    if ( current_user_can('subscriber') ) { 
	       add_filter('show_admin_bar', '__return_false');
	    }
	}
	public function active_deactive_users($actions, $user_object) {
	global $wpdb;
	if(isset($_GET['role']) && ($_GET['role']==='subscriber') && $_GET['action']=='deactivate' && !empty($_GET['user'])){
	$table = $wpdb->prefix."users";
	$updatedata=$wpdb->update( $table, array( 'user_status' => 0), array( 'ID' => $_GET['user'] ) );
    
		echo ("<script type='text/javascript'>
		alert('Deactivate successfully');

		window.location='".admin_url('users.php?role=subscriber')."';
		</script>");
	}

	if(isset($_GET['role']) && ($_GET['role']==='subscriber') && $_GET['action']=='activate' && !empty($_GET['user'])){
	$table = $wpdb->prefix."users";
	$updatedata=$wpdb->update( $table, array( 'user_status' => 1), array( 'ID' => $_GET['user'] ) );
    
	echo ("<script type='text/javascript'>
	alert('Activated successfully');

	window.location='".admin_url('users.php?role=subscriber')."';
	</script>");
	
	}
	// echo "<pre>"; print_r($user_object->roles[0]);
if(isset($_GET['role']) && ($_GET['role']==='subscriber')  && ($user_object->roles[0]=='subscriber')){

		
		if($user_object->user_status==1){
			$actions['edit_badges'] = "<a class='cgc_ub_edit_badges' href='" . admin_url( "users.php?role=subscriber&action=deactivate&amp;user=$user_object->ID") . "'>Deactivate</a>";
		}else{
		$actions['edit_badges'] = "<a class='cgc_ub_edit_badges' href='" . admin_url( "users.php?role=subscriber&action=activate&amp;user=$user_object->ID") . "'>Activate</a>";
		}
		return $actions;
	  }
	return $actions;
}
	public function prs_unsubscribe_shortcode() {
        if (isset($_POST['insert'])) {
            // echo $_GET['comment_id'];
        global $wpdb;
        
			$table = $wpdb->prefix."comments";
			$result=$wpdb->update( $table, array( 'notification' => 0,'reason' => $_POST['reason'] ), array( 'comment_ID' => $_GET['comment_id'] ) );
			if ($result) {

				echo "<h3 id='mydiv'>Thanks! You have unsubscribed successfully</h3>";
				?><script>
					setTimeout(function() {
					$('#mydiv').fadeOut('fast');
					}, 3000); 
				</script>
			<?php }else{
				echo "<h3 id='mydiv'>Whoops! Something went wrong</h3>";
			}
      
	 }
    ?>
    <!-- <link type="text/css" href="<?php //echo WP_PLUGIN_URL; ?>/product/style-admin.css" rel="stylesheet" /> -->

    <div class="wrap">
       
        <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <!-- <p>Three capital letters for the ID</p> -->
            <table class='wp-list-table widefat fixed'>
                
                
                
                <tr>
                    <th class="ss-th-width">Reason to unsubscribe</th>
                    <td><textarea type="text" name="reason" required="required"  class="ss-field-width" /></textarea></td>
                </tr>
                
            </table>
            <input type='submit' name="insert" value='Save' class='button'>
        </form>
    </div>
    
    <?php

    }
	 
	public function add_login_logout_link($items, $args) {
		if($args->menu->slug === 'nexus-header-menu')
		{
		    if(is_user_logged_in())
		    {
		        ob_start();
		        wp_loginout('user-registration?reason=loggedout');
		       $loginoutlink = ob_get_contents();
		       ob_end_clean();
		       $userid = get_current_user_id();
		        $upload_dir = wp_upload_dir();
			    $url = get_usermeta($userid , 'userphoto_thumb_file' );
			    if($url)
			    {
			    	 $avatar_html = '<img alt="" src="'.$upload_dir['baseurl'].'/userphoto/'.$url.'" srcset="'.$upload_dir['baseurl'].'/userphoto/'.$url.'" class="avatar avatar-64 photo" width="64" height="64">';
			    }
			    else
			    {
			    	 $url = get_usermeta($userid , 'starreview_facebook_id' );
			    	 $avatar_html = '<img alt="" src="https://graph.facebook.com/'.$url.'/picture?height=350&width=250" class="avatar avatar-64 photo" width="64" height="64">';
			    }
		       $items .= '<li class="menu-item">'.$avatar_html.''.$loginoutlink .'</li>';
		    }
		    else
		    {
		       $items .= '<li class="menu-item"><a href="'.site_url('user-registration').'">Login</a></li>';
		    }
		} 
	   return $items;
	}


// add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );



   public function wpse32218_check_for_key( $user, $username, $password ){

    

	    if ($username!=''){

	        	if( ! $user->user_status && $user->caps['subscriber']==1) {

				$error = new WP_Error( 'awaiting_activation', __("<strong>ERROR</strong>: You need to activate your account."."") );

				return $error;

			}

	    }

	    return $user;

	}



    public function starreview_comment_form()

    {

    	//front end custom comment form

    	require_once 'admin/list-table-example.php';
    	require_once 'comment-form/cust_comment_form.php';

    	$cust_comment_form = new cust_comment_form();

    	$cust_comment_form->register();



    	//custom comment text get comment meta

    	require_once 'comment-form/cust_comment_text.php';

    	require_once 'templates/starreview_registration.php';

    	$cust_comment_text = new cust_comment_text();

    	$cust_comment_text->register();



    	require_once 'star-prs-facebook.php';


    }


    public function admin_starreview_enqueue_script()
    {
    	wp_enqueue_script( 'starrview-admin-script', plugins_url( '/assets/js/starreview-admin-script.js' , __FILE__ ), ['jquery'], time(), true );
    }

    public function wp_starreview_enqueue_script()

    {



    	wp_enqueue_style( 'starreviewcss', plugins_url( '/assets/css/starreview.css', __FILE__ ) );



    	wp_enqueue_script( 'rating-validation-script', plugins_url( '/assets/js/validation-review-comment.js' , __FILE__ ), ['jquery'], time(), true );



    	wp_enqueue_style( 'starreviewcommentvalidation',  plugins_url( '/assets/css/comment-validation.css', __FILE__ ) );



		wp_enqueue_script('starreviewvalidate', plugins_url( '/assets/js/jquery-validation.js', __FILE__ ) , array('jquery'));

		// wp_enqueue_script('validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js',array('jquery'));

		wp_enqueue_script('starcommentvalidation', plugins_url( '/assets/js/comment-validation.js', __FILE__ ), array('jquery','starreviewvalidate'));

    }



   

	public function starreview_admin_page()

	{

		require_once "admin/stareview_admin_page.php";

		$starReviewAdminPage = new StarReviewAdminPage();

		$starReviewAdminPage->register();

	}



	public function starrating_setting_link($links)

	{

		$mylinks = array(

			'<a href="' . admin_url( 'admin.php?page=star-reviews-setting' ) . '">Settings</a>',

		);

		return array_merge( $links, $mylinks );

	}



}

if(class_exists('StarReview'))

{

	$StarReview = new StarReview();

	$StarReview->starreview_comment_form();

	$StarReview->starreview_admin_page();

}
