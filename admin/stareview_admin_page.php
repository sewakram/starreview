<?php
/**
 *@package StarReview
 */
ob_start();
/**
 * StarReview Admin Page
 */
class StarReviewAdminPage
{
	
	// function __construct()
	// {
	// 	# code...
	// }

	public function register()
	{

		add_action( 'admin_menu', array( $this, 'starreview_admin_menu_page' ) );
		add_action( 'edit_comment', array( $this, 'edit_settings' ) );	
		add_action( 'add_meta_boxes_comment', array( $this, 'register_metabox' ) );
		add_action('wp_ajax_isverified_comment', array($this, 'change_status_comment'));
		add_action('wp_ajax_nopriv_isverified_comment', array($this, 'change_status_comment'));
		
	}

	public function change_status_comment()
	{
		if($_POST['value'])
		{
			$_POST['value'] = 0;
		}
		else
		{
			$_POST['value'] = 1;
		}
		$postid = get_comment_meta( $_POST['commentid'], 'isVerified', 'single' );         
	    if($postid) { 
	    	 $_POST['value'] = 0;
	       	 update_comment_meta( $_POST['commentid'], 'isVerified', $_POST['value'] );
	    } else { 
	    	$_POST['value'] = 1;
	         update_comment_meta($_POST['commentid'], 'isVerified', $_POST['value'] );
	    }    
		echo $_POST['value'];
		wp_die();
	}
	public function starreview_admin_menu_page()
	{
		//add_menu_page( 'Star Review', 'Star Review', 'manage_options', 'star-reviews', array($this, 'star_review_dashboard') , "dashicons-palmtree", 101 );
	}

	//Dashboard page Star Reviews
	public function star_review_dashboard()
	{
		echo "This is Dashboard page";
	}

	public function register_settings( $comment ) {
		
		wp_nonce_field( 'starreview_update_rating', 'starreview_update_rating', false ); ?>
	
		<table id="gpur-review-details" class="form-table editcomment">
			<tbody>
					<tr>
						<td class="first">
							<label for="title">Review Title</label>
						</td>
						<td>	
							<input type="text" name="review_title" value="<?php echo esc_attr( get_comment_meta( $comment->comment_ID, 'review_title', true ) ); ?>" maxlength="60" />
						</td>
					</tr>	
	
				<tr>		
					<td class="first">
						<label for="rating">Rating</label>
					</td>
					<td>	
						<input type="text" name="rating" value="<?php echo esc_attr( get_comment_meta( $comment->comment_ID, 'rating', true ) ); ?>" />
					</td>
				</tr>
				<tr>
					<td class="first">
						<label for="rating"><?php esc_html_e( 'Up Votes', 'gpur' ); ?></label>
					</td>
					<td>
						<?php 
						$bottom_value = get_comment_meta( $comment->comment_ID, 'bottom_line', true );
						$checked1 = ''; $checked2 = ''; 
						if($bottom_value === 'y')
						{
							$checked1 = 'checked=checked';
						}
						else
						{
							$checked2 = 'checked=checked';
						}

						echo '<p><input id="bottom-line-yes" type="radio" name="bottom_line" value="y" '.$checked1.'/>'.esc_html__( 'Yes, I would recommend this to a friend', 'starreview' ).'</p><p><input id="bottom-line-no" type="radio" name="bottom_line" value="n" '.$checked2.' />'.esc_html__('No, I would not recommend this to a friend', 'starreview').'</p>'; ?>
					</td>
				</tr>
				<tr>
					<td class="first">
						<label for="rating"><?php esc_html_e( 'Review Helpful', 'starreview' ); ?></label>
					</td>
					<td>
						<input type="text" name="helpfull" value="<?php echo esc_attr( get_comment_meta( $comment->comment_ID, 'helpfull', true ) ); ?>" />
					</td>
				</tr>

			</tbody>		
		</table>		
		<?php
	}
	public function register_metabox() {
			add_meta_box( 'title', esc_html__( 'Review Details', 'gpur' ), array( $this, 'register_settings' ), 'comment', 'normal', 'high' );
	}
	public function edit_settings($comment_id)
	{
		if ( ! isset( $_POST['starreview_update_rating'] ) OR ! wp_verify_nonce( $_POST['starreview_update_rating'], 'starreview_update_rating' ) ) {
				return;
		}
		if ( ( isset( $_POST['review_title'] ) ) && ( $_POST['review_title'] != '' ) ) {
				$title = sanitize_text_field( $_POST['review_title'] );
				update_comment_meta( $comment_id, 'review_title', $title );
		} else {
			delete_comment_meta( $comment_id, 'review_title' );
		}

		if ( ( isset( $_POST['rating'] ) ) && ( $_POST['rating'] != '' ) ) {
				$title = sanitize_text_field( $_POST['rating'] );
				update_comment_meta( $comment_id, 'rating', $title );
		} else {
			delete_comment_meta( $comment_id, 'rating' );
		}

		if ( ( isset( $_POST['bottom_line'] ) ) && ( $_POST['bottom_line'] != '' ) ) {
				$title = sanitize_text_field( $_POST['bottom_line'] );
				update_comment_meta( $comment_id, 'bottom_line', $title );
		} else {
			delete_comment_meta( $comment_id, 'bottom_line' );
		}

		if ( ( isset( $_POST['helpfull'] ) ) && ( $_POST['helpfull'] != '' ) ) {
				$title = sanitize_text_field( $_POST['helpfull'] );
				update_comment_meta( $comment_id, 'helpfull', $title );
		} else {
			delete_comment_meta( $comment_id, 'helpfull' );
		}

	}

}