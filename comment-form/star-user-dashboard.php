<?php
/**
 *@package StarReview
 */
/**
 * user dashboard
 */

/**
 * 
 */
class StarUserDashboard
{

	public static function commonhead()
	{
		global $current_user, $wp_roles;
		echo "<span style='float:right'>
				<a href='".site_url( 'dashboard?ac=dashboard' )."'><em>Review</em></a> | 
				<a href='".site_url( 'dashboard?ac=account' )."'><em>Account Settings</em></a> | 
				<a href='".wp_logout_url('user-registration?reason=loggedout')."'><em>Logout</em></a>
			</span>
			</br>
			<div class='star-profile'>
					<span>"
						.get_the_author_meta( 'user_firstname', $current_user->id ).' '.get_the_author_meta( 'user_lastname', $current_user->id ).
					"</span>
				<div class='member' style='font-size:12px;color:#999;'>
					<span> "
						.get_the_author_meta( 'user_location', $current_user->id )." &nbsp; Member since ".date('F Y',strtotime($current_user->user_registered)).
					"</span>
				</div>
			</div>";
	}
	
	public static function Dashboard()
	{
		global $wpdb;
		$data = wp_get_current_user();
		$args = array( 'author_email' => $data->user_email);
		$comments = get_comments( $args );
		require_once('comment_list.php');
	}

	public static function AccountSetting()
	{
		global $current_user, $wp_roles;
		global $wp;
		$new_query = add_query_arg( array('ac' => 'profile'), $wp->request );
	?>
		<div class="entry-content entry">
            <?php if ( !is_user_logged_in() ) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                    </p><!-- .warning -->
            <?php else : ?>
                <form method="post" id="adduser" action="<?php echo $new_query;?>">
                    <p class="form-username">
                        <label for="first-name"><?php _e('Name', 'profile'); ?></label>
                        <input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'user_firstname', $current_user->id ); ?>" />
                    </p><!-- .form-username -->
		
                    <p class="form-email">
                        <label for="email"><?php _e('E-mail *(readonly)', 'profile'); ?></label>
                        <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->id ); ?>" readonly/>
                    </p><!-- .form-email -->
                    <p class="form-password">
                        <label for="pass1"><?php _e('Password *', 'profile'); ?> </label>
                        <input class="text-input" name="pass1" type="password" id="pass1" />
                    </p><!-- .form-password -->
                    <p class="form-password">
                        <label for="pass2"><?php _e('Repeat Password *', 'profile'); ?></label>
                        <input class="text-input" name="pass2" type="password" id="pass2" />
                    </p><!-- .form-password -->
                    <p class="form-location">
                        <label for="location"><?php _e('Location', 'profile'); ?></label>
                        <input class="text-input" name="location" type="text" id="location" value="<?php the_author_meta( 'user_location', $current_user->id ); ?>" />
                    </p><!-- .form-username -->
                    <p class="form-textarea">
                        <label for="description"><?php _e('Biographical Information', 'profile') ?></label>
                        <textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $current_user->id ); ?></textarea>
                    </p><!-- .form-textarea -->

                    <p class="form-submit">
                        <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profile'); ?>" />
                        <?php wp_nonce_field( 'update-user' ) ?>
                        <input name="action" type="hidden" id="action" value="update-user" />
                    </p><!-- .form-submit -->
                </form><!-- #adduser -->
            <?php endif; ?>
        </div><!-- .entry-content --> 
		<?php
	}


	public static function UpdateUserProcess(){
		global $current_user, $wp_roles;
		get_currentuserinfo();

		/* Load the registration file. */
		require_once( ABSPATH . WPINC . '/registration.php' );
		$error = array();    
		/* If profile was saved, update profile. */
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

			/* Update user password. */
			if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
				if ( $_POST['pass1'] == $_POST['pass2'] )
					wp_update_user( array( 'ID' => $current_user->id, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
				else
					$error[] = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
			}

			/* Update user information. */
			
			if ( !empty( $_POST['email'] ) ){
				if (!is_email(esc_attr( $_POST['email'] )))
					$error[] = __('The Email you entered is no valid.  please try again.', 'profile');
				elseif(email_exists(esc_attr( $_POST['email'] )) != $current_user->id )
					$error[] = __('Email not changed? your email is unique for comment review.', 'profile');
			}
			
			
			if ( !empty( $_POST['first-name'] ) )
				update_usermeta( $current_user->id, 'first_name', esc_attr( $_POST['first-name'] ) );

			if ( !empty( $_POST['location'] ) )
				update_usermeta( $current_user->id, 'user_location', esc_attr( $_POST['location'] ) );

			if ( !empty( $_POST['description'] ) )
				update_usermeta($current_user->id, 'description', esc_attr( $_POST['description'] ) );
		

			 if ( count($error) > 0 ) echo '<p class="error" style="color:red;font-size:15px;padding-left: 30px">*' . implode("<br />", $error) . '</p>';

			/* Redirect so the page will show updated info. */
			 if ( count($error) == 0 ) {
				//action hook for plugins and extra fields saving
				do_action('edit_user_profile_update', $current_user->id);
				//wp_redirect( get_permalink() );
				//exit;
				_e('<span style="color:green;font-size:15px;padding-left: 30px">Profile Update Successfully</span>');
			} 
		}
	}
}
if(is_user_logged_in())
{
	$callaction = new StarUserDashboard();
	$action	=	(isset($_REQUEST['ac'])) ? $_REQUEST['ac'] : 'none';
	switch($action){
		case 'dashboard':
			$callaction::commonhead();
			$callaction::Dashboard();
		break;
		case 'account':
			$callaction::commonhead();
			$callaction::AccountSetting();
		break;
		case 'profile':
			$callaction::UpdateUserProcess();
			$callaction::commonhead();
			$callaction::AccountSetting();
		break;
		default:
			$callaction::commonhead();
			$callaction::Dashboard();
		break;
	}
}
else
{
	echo '<h1 class="page-title" style="padding-top: 15px">Oops! That page can’t be found.</h1><br/><h2 style="padding-top: 15px; margin: 0">404 - No Results Found!</h2><p>The page you requested could not be found. Try refining your search, or use the navigation above to locate the post.</p>';
}

